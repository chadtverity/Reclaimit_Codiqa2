(function() {
  'use strict';

  var db;

  // Just a quick ajax OPTIONS wrapper to check connection.
  function checkOnline(url, method) {
    var deferred = new $.Deferred();
    $.ajax({
      url: url,
      type: method || 'OPTIONS',
      success: function() {
        deferred.resolve();
      },
      error: function(jqXHR, status, err) {
        deferred.resolve(status);
      }
    });
    return deferred.promise();
  }

  function serializeForm(form) {
    form = $(form);
	
	var a="";
	a=$(form).serializeArray();
	  
         $('input',form).each(function(){
	     if($(this).attr('type')=="checkbox"){
           			a= a.concat(
            $(this).map(
                    function() {
						var value;
						if(this.checked){
						 value=this.value;
						}
						else{value="";}
                       // return {"name": this.name, "value": ""}
                    }).get()
    );
		 }});
    
	 //ret=JSON.parse(ret);
	//  ret = JSON.stringify(eval("(" + ret + ")"));
	 //alert(ret);
    return {
      action: form.attr('action'),
      method: form.attr('method'),
      inputs: a,
      fileInputs: form.find('input[type="file"]').map(function(idx, elem) {
        return {
          name: elem.name,
          /* Turn FileList into Array because Firefox doesn't like
             cloning  FileLists. */
          files: $.map(elem.files, function(file) {
            return file;
          })
        };
      }).get()
    };
  }

  function saveForm(form) {
    var transaction = db.transaction(['forms'], 'readwrite');
    transaction.oncomplete = tryUpload;
    var formsObjStore = transaction.objectStore('forms');
	//alert(JSON.stringify(serializeForm(form)));
	
    var dbreq = formsObjStore.add(serializeForm(form));
  }

  function uploadForms(url) {
    var index = db.transaction(['forms']).objectStore('forms').index('action');
    index.openCursor().onsuccess = function(event) {
      var cursor = event.target.result;
      if (cursor) {
        if (cursor.key === url) {
          var formdata;
          var isPost = /POST/i.test(cursor.value.method);
          if(isPost) {
            formdata = new FormData();
            $.each(cursor.value.inputs, function(idx, input) {
				//alert(input.name+":"+input.value);
              formdata.append(input.name, input.value);
			  
			  
            });
			
            $.each(cursor.value.fileInputs, function(idx, input) {
              $.each(input.files, function(idx, file) {
                formdata.append(input.name, file);
              });
            });
          } else {
            formdata = [];
            $.each(cursor.value.inputs, function(idx, input) {
              formdata.push({
                name: input.name,
                value: input.value
              });
            });
          }
			//alert(JSON.stringify(formdata));
          var primaryKey = cursor.primaryKey;
		  
          $.ajax({
            url: cursor.key,
            type: cursor.value.method,
            success: function() {
              db.transaction(['forms'], 'readwrite').objectStore('forms')
                .delete(primaryKey);
            },
            data: formdata,
            cache: false,
            contentType: !isPost,
            processData: !isPost
          });
        }
        cursor.continue();
      }
    };
  }

  function tryUpload() {
    var index = db.transaction(['forms']).objectStore('forms').index('action');
    var c = index.openCursor(null, 'nextunique');
    c.onsuccess = function(event) {
      var cursor = event.target.result;
      if (cursor) {
        var action = cursor.key;
        $.when(checkOnline(action)).done(function(err) {
          if (!err) uploadForms(action);
        });
        cursor.continue();
      }
    };
  }

  $(document).ready(function() {
    // Setup Forms DB
    var dbreq = indexedDB.open('OfflineForms');

    dbreq.onsuccess = function(event) {
      window.db = db = event.target.result;
	 
    };
    dbreq.onupgradeneeded = function(event) {
      var db = event.target.result;
	  
      var objStore = db.createObjectStore('forms', {
        autoIncrement: true
      });
      objStore.createIndex('action', 'action', {
        unique: false
      });
    };

    // Setup offline forms
    $('form[data-offline]').submit(function(event) {
      event.preventDefault();

      if (this.enctype !== 'multipart/form-data' && this.enctype !== 'application/x-www-form-urlencoded') {
        throw new Error('Unsupported encoding ' + this.enctype + ' on form #' + this.id + '.');
      }

      saveForm(this);
      this.reset();

      alert('Form saved. If not connected it will be submitted once you\'re online.');
    });

    setInterval(tryUpload, 20000);
  });
}());
