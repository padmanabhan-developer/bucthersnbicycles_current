function ConfigurationInfo() {
	this.conf_id = 0;
	this.user_conf_id = 0;

	this.user_conf_name = '';
	this.user_conf_saved_date = '';

	this.setUserConfName = function(name) {
		this.user_conf_name = name;
	};

	this.getUserConfName = function() {
		return this.user_conf_name;
	};
}

var Conf = new ConfigurationInfo();