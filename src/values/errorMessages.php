<?php
class errorMessages{
	private $statusUnknown = array('Status' => "Message status is undefine, please check your parameter");
	private $status200 = array('Status' => "Ok");
	private $status404 = array('Status' => "Data not found");



    public function errorServerResponse($statusCode){
        switch ($statusCode) {
    case 200:
        return $this->status200; break;
    case 404:
    	return $this->status404; break;
    default:
        return $this->statusUnknown; break;
		}
	}
}