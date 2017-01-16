jQuery(function($) {
   BBVideoPlayer = {
    id: "",
    descr: "",
    full_id: "",
    full_descr: "",
    event: "",
    
    options: {
        playBtn: ".play-overlay",
        videoIdAttr: "data-video-id",
        container: ".bb_vimeo_videos",
        fullSizeVideo: "#full-size-video",
        fullVideoDescr: "#full-video-headline",
        smallVideoDescrClass: "other_video_headline"
    },
    
    init: function() {
        var that = this;
        $(this.options.playBtn).unbind("click");
        $(this.options.playBtn).click(function() {
            that.replaceVideos(this);
        });
        
        $(this.options.container).fitVids();
    },
    
    setOptions: function(e) {
        this.id = $(e).parent().find("img").attr(this.options.videoIdAttr),
        this.descr = $("#"+this.id+"_headline").text(),
        this.full_id = $(this.options.fullSizeVideo).attr(this.options.videoIdAttr),
        this.full_descr = $(this.options.fullVideoDescr).text();
    },
    
    replaceVideos: function(e) {
        this.setOptions(e);
        
        $(this.options.fullSizeVideo).replaceWith('<figure id="full-size-video" data-video-id="'+
                    this.id+'"><iframe src="http://player.vimeo.com/video/'+
                    this.id+'?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" width="474" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen"/></figure>');
            
        $(this.options.fullVideoDescr).replaceWith('<div class="full_video_headline" id="full-video-headline" data-video-id="'+this.id+'">'+this.descr+'</div>');
        
        this.event = e;
        this.getVideoThumb();
        
        this.init();
    },
    
    getVideoThumb: function() {
        this.ajaxCall({}, "http://vimeo.com/api/v2/video/"+this.full_id+".json", this.getVideoThumbSuccess);
    },
    
    getVideoThumbSuccess: function(data) {
        if(!data || !data[0] || !data[0].thumbnail_large)
            return;
        
//        this.full_image_url = data[0].thumbnail_large;

        var that = BBVideoPlayer,
            e = that.event,
            img = data[0].thumbnail_large;
        
        $(e).parent().find("img").replaceWith('<img src="'+img+'" data-video-id="'+that.full_id+'">');
        $("#"+that.id+"_headline").replaceWith('<div id="'+that.full_id+'_headline" class="'+that.options.smallVideoDescrClass+'">'+that.full_descr+'</div>');
        
        return img;
    },
    
    ajaxCall: function(data, url, success) {
        $.ajax({
           type: "get",
           dataType: "json",
           data: data,
           url: url,
           success: success
        });
    }
  }; 
});

jQuery(document).ready(function($) {
    BBVideoPlayer.init();
}); 