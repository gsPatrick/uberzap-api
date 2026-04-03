<?php 
Class buttons{
    public function button_status($status){
        $btn_array = array(
            "0" => "btn btn-outline-danger",
            '1' => "btn btn-warning",
            '2' => "btn btn-info",
            '3' => "btn btn-primary",
            '4' => "btn btn-success",
            '5' => "btn btn-outline-danger",
            '6' => "btn btn-outline-warning",
            '7' => "btn btn-outline-warning",
            '8' => "btn btn-outline-warningr",
            '9' => "btn btn-outline-warning",
            );
        return $btn_array[$status];
    }
}





?>