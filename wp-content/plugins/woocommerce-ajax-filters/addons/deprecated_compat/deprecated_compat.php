<?php
class BeRocket_aapf_deprecated_compat_addon extends BeRocket_framework_addon_lib {
    public $addon_file = __FILE__;
    public $plugin_name = 'ajax_filters';
    public $php_file_name   = 'widget';
    function get_addon_data() {
        $data = parent::get_addon_data();
        return array_merge($data, array(
            'addon_name'    => __('Deprecated Widget', 'BeRocket_AJAX_domain'),
            'deprecated'    => true,
            'tooltip'       => __('<span style="color: red;">DO NOT USE<br>IT WILL BE REMOVED IN THE FUTURE</span><br>Uses for compatibility with old filters', 'BeRocket_AJAX_domain')
        ));
    }
    function init_active() {
        parent::init_active();
        add_filter('BeRocket_AAPF_widget_load_file', array($this, 'disable_file'));
    }
    function disable_file($isload) {
        return false;
    }
}
new BeRocket_aapf_deprecated_compat_addon();
