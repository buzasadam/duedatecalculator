<?php

namespace Other;

class Log {

    const LOG_PATH = "/home/buzasadam/php_projects/Excercise/log/";

    public function m_log($arMsg)
    {
        //define empty string
        $stEntry="";
        //get the event occur date time,when it will happened
        $arLogData['event_datetime']='['.date('D Y-m-d h:i:s A').']';
        //if message is array type
        if(is_array($arMsg))
        {
            //concatenate msg with datetime
            foreach($arMsg as $msg)
                $stEntry.=$arLogData['event_datetime']." ".$msg."\r\n";
        }
        else
        {   //concatenate msg with datetime
                $stEntry.=$arLogData['event_datetime']." ".$arMsg."\r\n";
        }
        //create file with current date name
        $stCurLogFileName='log.txt';
        //open the file append mode,dats the log file will create day wise
        $fHandler=fopen(self::LOG_PATH.$stCurLogFileName,'a+');
        //write the info into the file
        fwrite($fHandler,$stEntry);
        //close handler
        fclose($fHandler);
    }

}
