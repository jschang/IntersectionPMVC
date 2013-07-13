<?php

class IPMVC_Portlet_TemplatedContent extends IPMVC_AbstractPortlet {
    private $resourceSelector;
    public function setResourceSelector(IPMVC_Resource_Selector $selector) {
        $this->resourceSelector = $selector;
    }
    public function process(IPMVC_Portlet_Request $request) {
    }
    public function render(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response) {
        $response->setContent($this->resourceSelector->getResource($this->getURI())->getContent());
    }
}
