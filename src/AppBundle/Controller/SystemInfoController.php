<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tivie\GitLogParser\Parser;

class SystemInfoController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $request = Request::createFromGlobals();

        $server = $request->server;
        $server->remove('Path');

        $php_version = phpversion();

        exec('lsb_release -a', $operating_system);
        exec('uname -r', $linux_core);

        $symfony_version = \Symfony\Component\HttpKernel\Kernel::VERSION;


        $parser = new Parser();
        $logArray = $parser->parse();

        $commit = array_shift($logArray);


        return $this->render('Info/SystemInfo/index.html.twig',
            compact('server', 'symfony_version', 'php_version', 'operating_system', 'commit', 'linux_core'));
    }
}