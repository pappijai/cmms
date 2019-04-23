$professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');
                            
                            // check if the professor is available for the given day
                            if(empty($professor_subject_schedule_save)){
                                $can_sched = true;
                            }
                            else{
                                for($i = 0;$i < count($professor_subject_schedule_save);$i++){
                                    $j = $i;

                                    // check if the generated start time is available in the professor schedule
                                    if($request->Time_in1 >= $professor_subject_schedule_save[$i]->STSTimeEnd){
                                        
                                        $j++;

                                        // check if the schedule subject of professor is empty
                                        if(!isset($professor_subject_schedule_save[$j])){
                                            $can_sched = true;
                                            break 1;
                                        }// end of check if the schedule subject of professor is empty
                                        else{
                                            if($Time_Out1 <= $professor_subject_schedule_save[$j]->STSTimeStart){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                $can_sched = false;
                                                // break;
                                                $and_di_pwede ='Professor is not Available';
                                                
                                                
                                            }
                                        }
                                        
                                    }// end of check if the generated start time is available in the professor schedule
                                    else{
                                        if($Time_Out1 <= $professor_subject_schedule_save[$i]->STSTimeStart){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            //break;
                                            $and_di_pwede ='Professor is not Availablee';
                                        }
                                        
                                    }
                                }// end of loop of the subject and schedule that this professor have     
                            }