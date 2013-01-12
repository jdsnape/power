<?php

require("config.php");
$pulses=array();$i=0;

$fromdate= $_POST["fromdate"];
$fromdate_parts=explode("/",$fromdate);

$fromdate_unix=mktime(0,0,0,$fromdate_parts[1],$fromdate_parts[0],$fromdate_parts[2]);

$todate = $_POST["todate"];
$todate_parts = explode("/",$fromdate);
$todate_unix = mktime(23,59,59,$todate_parts[1],$todate_parts[0],$todate_parts[2]);

class PulseDB extends SQLite3 { function __construct() { $this->open(SQLITE_DB); } }

$_database=new PulseDB();
                $_query=$_database->query("select datetime(stamp,'unixepoch','localtime') as blink from pulse where stamp > '".$fromdate_unix."' and stamp <'".$todate_unix."';");

         while($_result=$_query->fetchArray(SQLITE3_ASSOC))
                { $pulses[$i]=$_result["blink"]; $i++; }
        $_database->close();

//var_dump(substr($pulses[count($pulses)-1],11,2));

  $_hours=array(); $_consumption=array(); $js=array();
        for($i=0;$i<substr($pulses[count($pulses)-1],11,2);$i++) //loop through on an hour by hour basis up to max hour from returned results.
        {
                $h=($i<10?"0".$i:$i);
                $_group=array(); $j=0;
                for($k=0;$k<count($pulses);$k++)
                {
                        if(substr($pulses[$k],11,2)==$h) { $_group[$j]=$pulses[$k]; $j++; }
                }
                if(count($_group)>0)
                {
                        $_hours[$i]=$_group;
                        $_time=3600/count($_group);
                        $_consumption[$i]=round((3600/($_time*IMPKWH)),4);
                } else {
                        $_consumption[$i]=0;
                }
        }
echo json_encode($_consumption);
//var_dump($_consumption);
/*
        //$js="var consumption=[";
        for($i=0;$i<count($_consumption);$i++)
        {
                $h=($i<10?"0".$i:$i);
                $js[((new Date(\"".date('Y/m/d')." ".$h.":00:00\")).getTime()]=$_consumption[$i];
                if($i!=(count($_consumption)-1)) { $js.=","; }
        }
        //$js.="];\n";



var_dump($js);*/
?>
