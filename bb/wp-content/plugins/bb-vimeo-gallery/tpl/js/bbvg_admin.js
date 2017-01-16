BBVimeoGalleryAdmin = {

	counter: 0,

	init: function() {
		var that = this;
        jQuery("#bb_vg_add_btn").unbind("click");
		jQuery("#bb_vg_add_btn").click(function() {
			that.addVideoInput();
		});
        
        jQuery(".remove_other_video_inp").unbind("click");
        jQuery(".remove_other_video_inp").click(function(event){
            that.removeVideoInput(event);
        });
	},
	
	countElements: function(selector) {
		var that = this;
		this.counter = 0;
		jQuery(selector).each(function() {
			that.counter++;
		});
	},
	
	addVideoInput: function() {
		this.countElements('.other_video_inp');
		var html = '<div class="other_video_inp" id="other_video_inp_'+this.counter+'">'+
                '<label for="bb_vimeo_other_videos['+this.counter+'][id]">ID:</label>'+
                '<input type="text" name="bb_vimeo_other_videos['+this.counter+'][id]" value="">'+
                '<label for="bb_vimeo_other_videos['+this.counter+'][title]">Headline:</label>'+
                '<input type="text" name="bb_vimeo_other_videos['+this.counter+'][title]" value="">'+
                '<input type="button" class="remove_other_video_inp" value="Remove" data-inp-id="'+this.counter+'">'+
            '</div>';
		jQuery('.other_videos_container').append(html);
        
        this.init();
	},
    
    removeVideoInput: function(event) {
        var id = jQuery(event.target).attr("data-inp-id");
        jQuery("#other_video_inp_"+id).remove();
    }
};

jQuery(document).ready(function() {
	BBVimeoGalleryAdmin.init();
});