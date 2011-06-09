<?php

class ProjectController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headScript()->appendFile( '/js/project.js');
    }

    public function indexAction()
    {

        $request = $this->getRequest ();

        $linkRewrite=$request->project;

        $dbProject=new Application_Model_DbTable_Projects();


        //->from(array("proyectos"), array("*","days"=>"abs(datediff(now(),p.fec_fin)"))
        $project=$dbProject->fetchRow(
                $dbProject->select()
                ->from(array("proyectos"), array("*","days"=>new Zend_Db_Expr("abs(datediff(now(),fec_fin))")))
                ->where('link_rewrite = "'.$linkRewrite.'"')
                );

        if(count($project)==0)
            $this->_redirect ( '/');
        else{
            $dbReward=new Application_Model_DbTable_Rewards();
            $rewards=$dbReward->fetchAll(
                $dbReward->select()
                ->where('id_proyecto = '.$project->id_proyecto)
                );


            $dbSupport =new Application_Model_DbTable_Support();
            $supports=$dbSupport->fetchRow(
                    $dbSupport->select()
                    ->from("apoyo",
                            array('sum(apoyo) as sum_apoyo','count(apoyo) as count_apoyo', 'apoyo'))
                    ->where('id_proyecto = '.$project->id_proyecto)
                    ->group('apoyo')
                    );
            
            $this->view->recaudado=isset($supports->sum_apoyo)?$supports->sum_apoyo:0;
            $this->view->numApoyos=isset($supports->count_apoyo)?$supports->count_apoyo:0;
            $this->view->rewards=$rewards;
            $this->view->project=$project;
            $this->view->porcentaje=($supports->apoyo/$project->importe_solicitado)*100;
            //$now=new Datetime();
            //$interval = $this->dateDifference(date(), $project->fec_fin);
            $this->view->days=$project->days;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
        }
        
    }

    private function dateDifference($startDate, $endDate)
        {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
            if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
                return false;

            $years = date('Y', $endDate) - date('Y', $startDate);

            $endMonth = date('m', $endDate);
            $startMonth = date('m', $startDate);

            // Calculate months
            $months = $endMonth - $startMonth;
            if ($months <= 0)  {
                $months += 12;
                $years--;
            }
            if ($years < 0)
                return false;

            // Calculate the days
                        $offsets = array();
                        if ($years > 0)
                            $offsets[] = $years . (($years == 1) ? ' year' : ' years');
                        if ($months > 0)
                            $offsets[] = $months . (($months == 1) ? ' month' : ' months');
                        $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

                        $days = $endDate - strtotime($offsets, $startDate);
                        $days = date('z', $days);

            return array($years, $months, $days);
        }


}

