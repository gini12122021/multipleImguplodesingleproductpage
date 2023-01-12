jQuery('document').ready(function(){
    var ajaxURL = cpo_data.ajax_url;

    /*Show img in uploade preview in single page*/

    jQuery(document).on('change','.upload_file',function(e){
    	e.preventDefault();

        if (this.files && this.files[0] && this.files[0].name.match(/\.(jpg|jpeg|png|pdf|doc|docx)$/) ) {
                var files = e.target.files;
				filesLength = files.length;
				var data = files[0].name;
				var arr = data.split('.');
				var extentionmane=arr[1];
                for (var i = 0; i < filesLength; i++) {
					var f = files[i];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					  var file = e.target;
                     
                      jQuery('#image_preview').append("<div class=\"pip imguplodecust\"><div class=\"imginnerblock\">" +
                      "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + data + "\"/> <span class=\"imgtitlename\">" +data+
                      "</span></div><span class=\"remove\"><i class=\"removeicon\" ></i></span>" +
                      "</div>");
                      jQuery(".remove").click(function(){
                  
                      jQuery(this).parent(".pip").remove();
                    });
                    
                    jQuery('<input type="hidden" name="wc_uplode_img[]" data-id="'+data+'" value="'+ e.target.result+'">').insertAfter("#image_preview");
					  
					 
					  
					});
					fileReader.readAsDataURL(f);
				}
        }
        else{
            alert('This is not an image file!'); 
        }

    });


    jQuery(document).on('change','.prefix-cart-image',function(e){
		e.preventDefault();
        var idcurent=jQuery(this).attr('id');
        if (this.files && this.files[0] && this.files[0].name.match(/\.(jpg|jpeg|png|pdf|doc|docx)$/) ) {
            var files = e.target.files;
            filesLength = files.length;
            var data = files[0].name;
            var arr = data.split('.');
            var extentionmane=arr[1];
            for (var i = 0; i < filesLength; i++) {
                var f = files[i];
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                  var file = e.target;
                 
                  jQuery("<span class=\"pip imgcartpip\">" +
                  "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                  "<span class=\"remove\"><i class=\"removei\">×</i></span>" +
                  "</span>").insertAfter("#"+idcurent);
                  jQuery(".remove").click(function(){
              
                  jQuery(this).parent(".pip").remove();
                });
                
                  
                 
                  
                });
                fileReader.readAsDataURL(f);
            }
    }
    else{
        alert('This is not an image file!'); 
    }
        var fd = new FormData();
        jQuery('.cart_totals').block({
            message: null,
            overlayCSS: {
            background: '#fff',
            opacity: 0.6
            }
        });
        var cart_id = jQuery(this).data('cart-id');
        var files = jQuery('#cart_notes_'+cart_id)[0].files;
        if(files.length > 0 ){
            fd.append('image',files[0]);
            fd.append('cart_id',cart_id);
            fd.append('security',jQuery('#woocommerce-cart-nonce').val());
            fd.append('action','cpo_update_cart_imguplode_custome');
            jQuery.ajax({
                type: 'POST',
                url: ajaxURL,
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function( response ) {
                    if(response.success == 1){
                        location.reload(); 
                        jQuery('.cart_totals').unblock();
                     }
                }
                
            })
         
        }
        else{

            alert('Please Uplode correct formate.');
        }

        
    }); 



    jQuery(document).on('click','.cpo_removeimg',function(e){
		e.preventDefault();
        var cart_id = jQuery(this).data('cartid');
        var removesrc = jQuery(this).data('src');
        var str =  '&cart_id='+cart_id+'&imgsrc='+ removesrc +'&action=cpo_remove_imgcartid';
        jQuery.ajax({
            type: 'POST',
            url: ajaxURL,
            data: str,
            success: function( response ) {
               if(response.success == 1){
                    location.reload(); 
                    jQuery('.cart_totals').unblock();
                    }
            }
            
            })
      
    });

});

