<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ServerLogsController extends Controller
{
    /**
     * @var string
     */
    private $allLogs = 'all';

    /**
     * @var string
     */
    private $criticalLogs = 'CRITICAL';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(){

        $dev_log = 'var/logs/prod.log';

        try{
            $file_array = file(__DIR__ . '/../../../' . $dev_log);
        } catch (\Exception $exception){
            $fewLogs = [];
            return $this->render('Info/ServerLogs/index.html.twig',compact('fewLogs'));
        }


        $logs = [];
        for ($i = max(0, count($file_array)-20); $i < count($file_array); $i++) {
            $logs[] = $file_array[$i];
        }

        $revers = array_reverse($logs);

        $fewLogs = array_slice($revers, 0 ,20);

        return $this->render('Info/ServerLogs/index.html.twig',compact('fewLogs'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logFilterAction(Request $request){

        $typeLog = $request->get('type');
        $valueLog = $request->get('value');
        $fileLog = $request->get('file');

        $dev_log = 'var/logs/'. $fileLog. '.log';

        try{
            $file_array = file(__DIR__ . '/../../../' . $dev_log);
        } catch (\Exception $exception){
            $fewLogs = [];
            return $this->render('Info/ServerLogs/index.html.twig',compact('fewLogs'));
        }

        if ($this->allLogs == $typeLog){
            $logs = $this->allLogs($file_array, $valueLog);
        } elseif ($this->criticalLogs == $typeLog){
            $logs = $this->criticalLogs($file_array, $typeLog);
        } else {
            $logs = [];
            for ($i = max(0, count($file_array)-$valueLog); $i < count($file_array); $i++) {

                $pos = strpos($file_array[$i], $typeLog);
                if($pos !== FALSE){
                    $logs[] = $file_array[$i];
                }
            }
        }

        $revers = array_reverse($logs);

        $fewLogs = array_slice($revers, 0 ,$valueLog);

        return $this->render('Info/ServerLogs/index.html.twig',compact('fewLogs'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(){

        try{
            $dev = fopen(__DIR__ . '/../../../var/logs/dev.log','w+');
            $prod = fopen(__DIR__ . '/../../../var/logs/prod.log','w+');
            fclose($dev);
            fclose($prod);
        } catch (\Exception $exception){

            return $this->redirect('/admin/server_logs');
        }

        return $this->redirect('/admin/server_logs');
    }


    /**
     * @param array $file_array
     * @param int $valueLog
     * @return array
     */
    public function allLogs(array $file_array,$valueLog = 20){

        $logs = [];
        for ($i = max(0, count($file_array)-$valueLog); $i < count($file_array); $i++) {

                $logs[] = $file_array[$i];
        }
        return $logs;
    }

    /**
     * @param array $file_array
     * @param $typeLog
     * @return array
     */
    public function criticalLogs(array $file_array,$typeLog){
        $logs = [];
        foreach($file_array as $item) {
            $pos = strpos($item, $typeLog);
            if($pos !== FALSE){
                $logs[] = $item;
            }
        }

        return $logs;
    }

}