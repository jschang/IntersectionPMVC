<?php

class IPMVC_Portlet_TemplatedContent extends IPMVC_AbstractPortlet {
    public function process(IPMVC_Portlet_Request $request) {
        //die('in process');
    }
    public function render(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response) {
        $response->setContent("lkjasdf");
    }
}
