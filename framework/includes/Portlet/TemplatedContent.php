<?php

class IPMVC_Portlet_TemplatedContent extends IPMVC_AbstractPortlet {
    private $resourceSelector;
    public function setResourceSelector(IPMVC_Resource_Selector $selector) {
        $this->resourceSelector = $selector;
    }
    public function process(IPMVC_Portlet_Request $request) {
    }
    public function render(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response) {
        ob_start();
        $this->includeFile($this->getURI());
        $content = ob_get_contents();
        ob_end_clean();
        $response->setContent($content);
    }
    public function includeFile($resourceUri) {
        $content = $this
            ->resourceSelector
            ->getResource($resourceUri)
                ->getContent();
        eval('?>'.$content.'<?');
    }
}
