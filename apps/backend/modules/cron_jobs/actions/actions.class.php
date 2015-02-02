<?php

/**
 * cron_jobs actions.
 *
 * @package    zapnacrm
 * @subpackage cron_jobs
 * @author     Your name here
 */
class cron_jobsActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->cron_jobs_list = CronJobsPeer::doSelect(new Criteria());
    }

    public function executeNew(sfWebRequest $request) {
        $types = CronTypesPeer::doSelect(new Criteria());
        $this->types = $types;


        $this->form = new CronJobsForm();
    }

    public function executeCreate(sfWebRequest $request) {
        //die("I m here");
        $this->forward404Unless($request->isMethod('post'));
        $minutes = $request->getParameter("minutes");
        $minutesSelect = implode(",", $request->getParameter("selectMinutes"));
        $hours = $request->getParameter("hours");
        //$hoursSelect = implode(",", $request->getParameter("selectHours"));
        $days = $request->getParameter("days");
        $daysSelect = implode(",", $request->getParameter("selectDays"));
        $months = $request->getParameter("months");
        $monthsSelect = implode(",", $request->getParameter("selectMonths"));
        $weekdays = $request->getParameter("weekdays");
        $weekdaysSelect = implode(",", $request->getParameter("selectWeekdays"));

        $cron_type = $request->getParameter("cron_type");
        $cronTypeObj = CronTypesPeer::retrieveByPK($cron_type);
        $new_cron = new CronJobs();
        $new_cron->setCronTypeId($cron_type);

        $job_name = $request->getParameter("job_name");
        $email = $request->getParameter("email");

        $emails = explode(";", $email);
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //valid
            } else {
                $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Please enter valid email address(es).'));
                $this->redirect('cron_jobs/new');
            }
        }

        $directory_base = "/home/dbnposcms/";
        $defination_file_path = $request->getParameter("defination_file_path");
        $data_file_path = $request->getParameter("data_file_path");
        $defination_file_path = str_replace(' ', '', $defination_file_path);
        $data_file_path = str_replace(' ', '', $data_file_path);

        $full_defination_file_path = $directory_base . $defination_file_path;
        $full_data_file_path = $directory_base . $data_file_path;
        //echo strpbrk($full_defination_file_path, "\\/?%*:|\"<>");
        if (strpbrk($full_defination_file_path, "\\?%*:|\"<>") === FALSE) {
            /* $filename is legal; doesn't contain illegal character. */
        } else {
            /* $filename contains at least one illegal character. */
            $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Defination file directory path is invalid.'));
            $this->redirect('cron_jobs/new');
        }


        if (strpbrk($full_data_file_path, "\\?%*:|\"<>") === FALSE) {
            /* $filename is legal; doesn't contain illegal character. */
        } else {
            /* $filename contains at least one illegal character. */
            $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Data file directory path is invalid.'));
            $this->redirect('cron_jobs/new');
        }

        if (substr($defination_file_path, 0, 1) == "/") {
            $defination_file_path = substr($defination_file_path, 1);
        }
        if (substr($data_file_path, 0, 1) == "/") {
            $data_file_path = substr($data_file_path, 1);
        }
        $new_cron->setEmail($email);
        $new_cron->setJobName($job_name);
        $new_cron->setDefinationFilePath($defination_file_path);
        $new_cron->setDataFilePath($data_file_path);
        $new_cron->setMinutes($minutes);
        $new_cron->setCustomMinutes($minutesSelect);

        $new_cron->setHours($hours);
        //$new_cron->setCustomHours($hoursSelect);

        $new_cron->setDays($days);
        $new_cron->setCustomDays($daysSelect);

        $new_cron->setMonths($months);
        $new_cron->setCustomMonths($monthsSelect);


        $new_cron->setWeekdays($weekdays);
        $new_cron->setCustomWeekdays($weekdaysSelect);



        if ($minutes == "select") {
            $minutes = $minutesSelect;
        }
        if ($hours == "select") {
            $hours = $hoursSelect;
        }
        if ($days == "select") {
            $days = $daysSelect;
        }
        if ($months == "select") {
            $months = $monthsSelect;
        }
        if ($weekdays == "select") {
            $weekdays = $weekdaysSelect;
        }

        $cron_string = $minutes . " " . $hours . " " . $days . " " . $months . " " . $weekdays . " " . " chmod 777 -R " . $full_defination_file_path . " && chmod 777 -R " . $full_data_file_path . " && " . $cronTypeObj->getUrl();

        $new_cron->setJob($cron_string);

        $new_cron->save();

        $cron_stringntr = $new_cron->getJob() . "?id=" . $new_cron->getId();
        $new_cron->setJob($cron_stringntr);

        $new_cron->save();

       // $this->update_cron_job_on_server();

        $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('Job Successfully Added.'));
        $this->redirect('cron_jobs/index');
    }
    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($cron_jobs = CronJobsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object cron_jobs does not exist (%s).', $request->getParameter('id')));
        // $this->form = new CronJobsForm($cron_jobs);
        $this->cron_jobs = $cron_jobs;
        $this->types = CronTypesPeer::doSelect(new Criteria());
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $minutes = $request->getParameter("minutes");
        $minutesSelect = implode(",", $request->getParameter("selectMinutes"));
        $hours = $request->getParameter("hours");
        $hoursSelect = implode(",", $request->getParameter("selectHours"));
        $days = $request->getParameter("days");
        //$daysSelect = implode(",", $request->getParameter("selectDays"));
        $months = $request->getParameter("months");
        $monthsSelect = implode(",", $request->getParameter("selectMonths"));
        $weekdays = $request->getParameter("weekdays");
        $weekdaysSelect = implode(",", $request->getParameter("selectWeekdays"));

        $cron_type = $request->getParameter("cron_type_id");
        $cron_job_id = $request->getParameter("cron_job_id");

        $cronTypeObj = CronTypesPeer::retrieveByPK($cron_type);

        $c = new Criteria();
        $c->add(CronJobsPeer::ID, $cron_job_id);

        $new_cron = CronJobsPeer::doSelectOne($c);


        $defination_file_path = $request->getParameter("defination_file_path");
        $data_file_path = $request->getParameter("data_file_path");
        $defination_file_path = str_replace(' ', '', $defination_file_path);
        $data_file_path = str_replace(' ', '', $data_file_path);

        $job_name = $request->getParameter("job_name");
        $email = $request->getParameter("email");

        $emails = explode(";", $email);
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //valid
            } else {
                $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Please enter valid email address(es).'));
                $this->redirect('cron_jobs/edit?id=' . $cron_job_id);
            }
        }


        $directory_base = "/home/dbnposcms/";
        

        $full_defination_file_path = $directory_base . $defination_file_path;
        $full_data_file_path = $directory_base . $data_file_path;
        //echo strpbrk($full_defination_file_path, "\\/?%*:|\"<>");
        if (strpbrk($full_defination_file_path, "\\?%*:|\"<>") === FALSE) {
            /* $filename is legal; doesn't contain illegal character. */
        } else {
            /* $filename contains at least one illegal character. */
            $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Defination file directory path is invalid.'));
            $this->redirect('cron_jobs/edit?id=' . $cron_job_id);
        }


        if (strpbrk($full_data_file_path, "\\?%*:|\"<>") === FALSE) {
            /* $filename is legal; doesn't contain illegal character. */
        } else {
            /* $filename contains at least one illegal character. */
            $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Data file directory path is invalid.'));
            $this->redirect('cron_jobs/edit?id=' . $cron_job_id);
        }

        if (substr($defination_file_path, 0, 1) == "/") {
            $defination_file_path = substr($defination_file_path, 1);
        }
        if (substr($data_file_path, 0, 1) == "/") {
            $data_file_path = substr($data_file_path, 1);
        }


        $new_cron->setEmail($email);
        $new_cron->setJobName($job_name);
        $new_cron->setDefinationFilePath($defination_file_path);
        $new_cron->setDataFilePath($data_file_path);



        $new_cron->setCronTypeId($cron_type);

        $new_cron->setMinutes($minutes);
        $new_cron->setCustomMinutes($minutesSelect);

        $new_cron->setHours($hours);
        $new_cron->setCustomHours($hoursSelect);

        $new_cron->setDays($days);
        //$new_cron->setCustomDays($daysSelect);

        $new_cron->setMonths($months);
        $new_cron->setCustomMonths($monthsSelect);

        $new_cron->setWeekdays($weekdays);
        $new_cron->setCustomWeekdays($weekdaysSelect);

        if ($minutes == "select") {
            $minutes = $minutesSelect;
        }
        if ($hours == "select") {
            $hours = $hoursSelect;
        }
        if ($days == "select") {
            $days = $daysSelect;
        }
        if ($months == "select") {
            $months = $monthsSelect;
        }
        if ($weekdays == "select") {
            $weekdays = $weekdaysSelect;
        }
        
        $cron_string = $minutes . " " . $hours . " " . $days . " " . $months . " " . $weekdays . " " . " chmod 777 -R " . $full_defination_file_path . " && chmod 777 -R " . $full_data_file_path . " && " . $cronTypeObj->getUrl();
        $new_cron->setJob($cron_string);
        $new_cron->save();
        $cron_stringntr = $new_cron->getJob() . "?id=" . $new_cron->getId();
        $new_cron->setJob($cron_stringntr);

        $new_cron->save();

       // $this->update_cron_job_on_server();
        $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('Job Successfully Updated.'));
        $this->redirect('cron_jobs/index');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($cron_jobs = CronJobsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object cron_jobs does not exist (%s).', $request->getParameter('id')));
        $cron_jobs->delete();
        //$this->update_cron_job_on_server();
        $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('Job Deleted Successfully.'));
        $this->redirect('cron_jobs/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $cron_jobs = $form->save();

            $this->redirect('cron_jobs/edit?id=' . $cron_jobs->getId());
        }
    }

    private function update_cron_job_on_server() {
        $sshManager = new Ssh2_crontab_manager('184.107.168.18', '22', 'dbnposcms', 'zap#@!SAH');
        $cronjobs = CronJobsPeer::doSelect(new Criteria());
        $jobs = "";
        foreach ($cronjobs as $cronjob) {
            if ($cronjob->getJob() != "") {
                $jobs .= $cronjob->getJob() . " \n";
            }

            $c1 = "mkdir -p " . $cronjob->getDefinationFilePath() . "/backup && mkdir -p " . $cronjob->getDataFilePath() . "/backup";
            $c2 = "mkdir -p " . $cronjob->getDefinationFilePath() . "/error && mkdir -p " . $cronjob->getDataFilePath() . "/error";
            $sshManager->exec($c1);
            $sshManager->exec($c2);
        }
        $command = 'echo "' . $jobs . '" > ~/cronjobs.txt';

        //echo $command;

        $sshManager->exec($command);
        //echo "<br/>";
        $cronJobsCommand = "crontab ~/cronjobs.txt";
        //echo $cronJobsCommand;
        $sshManager->exec($cronJobsCommand);

        $delCronFileCommand = "rm ~/cronjobs.txt";
        $sshManager->exec($delCronFileCommand);
    }
    
    public function executeHistory(sfWebRequest $request) {
        $c = new Criteria();
        $c->add(CronJobHistoryPeer::CRON_JOB_ID,$request->getParameter('id'));
        $cronJob = CronJobsPeer::retrieveByPK($request->getParameter('id'));
        $this->cron_job_id = $cronJob->getId();
        $this->job_name = $cronJob->getJobName();
//        $cronHistories = CronJobHistoryPeer::doSelect($c);
         $this->close_url="http://dbnposcms.zap-itsolutions.com/backend.php/cron_jobs/index";
    }

      public function executeHistoryInfo(sfWebRequest $request) {
        
        $this->history_id =$request->getParameter('history_id');
        $this->close_url="http://dbnposcms.zap-itsolutions.com/backend.php/cron_jobs/history/id/".$request->getParameter('cron_job_id');
    }
    
    
}
