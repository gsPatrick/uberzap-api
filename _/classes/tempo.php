<?php 
Class tempo{
    public $data_ini;
    public $data_fim; 

    public function __construct(){
        $this->data_ini = date('Y-m-d H:i:s');
    }
   
    private function ePassado($data, $valor){
        $data = strtotime($data);
        $data_fim = strtotime($this->data_fim);
        if($data_fim > $data){
            return "-".$valor;
        }else{
            return $valor;
        }
    }

   public function tempo_passou($tipo = "minutos"){
    $to_time = strtotime($this->data_fim);
	$from_time = strtotime($this->data_ini);
    $minutos = round(abs($to_time - $from_time) / 60,);
    $horas = round(abs($to_time - $from_time) / 3600,);
    $dias = round(abs($to_time - $from_time) / 86400,);
    if($tipo == "minutos"){
        return $this->ePassado($this->data_ini, $minutos);
    }else if($tipo == "horas"){
        return $this->ePassado($this->data_ini, $horas);
    }else if($tipo == "dias"){
        return $this->ePassado($this->data_ini, $dias);
    }
   }

   public function data_mysql_para_user($data){
    $data = explode(" ", $data);
    $data = explode("-", $data[0]);
    $data = $data[2]."/".$data[1]."/".$data[0];
    return $data;
   }

   public function hora_mysql_para_user($hora){
    $hora = explode(" ", $hora);
    $hora = $hora[1];
    return $hora;
   }

   public function data($data){
    $data = explode(" ", $data);
    return $data[0];
   }

}






?>