jQuery.fn.fileupload = function(opt){
  $this = jQuery(this);
  set = {
    'url':'http://new.7772977.ua/index.php?option=com_forms&task=upload_file&format=raw',
    'dataType':'json'
  }
  if(opt){ jQuery.extend(set,opt); }
  return this.each(function(){
    $this.change(function(){
      xhr = new XMLHttpRequest;
      xhr.open("POST", set.url+'&qqfile='+$this.val()+'&id='+$this.attr("id"), true);
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xhr.setRequestHeader("X-File-Name", encodeURIComponent($this.val()));
      xhr.setRequestHeader("Content-Type", "application/octet-stream");
	  xhr.send(this.files[0]);
      xhr.onreadystatechange = function(){
		
        if (xhr.readyState == 4){ 
          if(set.dataType == 'json'){
              response = jQuery.parseJSON(xhr.responseText);
			  if(response == null) { response = {}; } 
          }else{
            response = xhr.responseText;
          }
          set.success.call("",response);
        }
      }
    });
  });
};
