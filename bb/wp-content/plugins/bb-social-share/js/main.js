SocialShare = {
	share_to: "",
	title: "",
	description: "", 
	image: "",
	url: "",
	shareUrl: "",

	init: function() {
		var that = this;

		jQuery(".share-square").unbind("click");
		jQuery(".share-square").click(function(event) {
			console.log(event);
			that.share_to = str = event.target.classList[2].substr(13);
			
			that.getShareLink();
		});
	},

	showShareButton: function(selector, showWhereSelector) {
		jQuery(selector).hover(function() {
			jQuery(showWhereSelector).fadeToggle("slow");
		});
	},

	ajaxCall: function(data, success) {
		jQuery.ajax({
			type: "get",
			url: "/wp-content/plugins/bb-social-share/index.php",
			dataType: "json",
			async: false,
			data: data,
			success: success
		});
	},

	getShareLink: function() {
		var data = {
			share_to: this.share_to,
			og_title: this.title,
			og_descr: this.description,
			share_url: window.location.href,
			og_image: this.image
		};

		this.ajaxCall(data, this.getShareLinkSuccess);
	},

	getShareLinkSuccess: function(data) {
		window.open(data, "Share", "width=425,height:400");
		jQuery('#bbshare_popup_dialog').dialog("close");
	}
};

jQuery(function() {
	jQuery("#bbshare_popup_dialog").dialog({
		autoOpen: false,
		modal: true,
		width: 210,
		height: 125
	});
});

jQuery(document).ready(function() {
	SocialShare.init();
});