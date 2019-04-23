            foreach($classroom_available as $row){
                $can_sched = false;

                $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
                                            ClassroomID = "'.$row->ClassroomID.'" AND 
                                            STSStatus = "Active" AND
                                            STSDay = "'.$request->Day1.'" ORDER BY STSTimeStart ASC');

                // check if the classroom selected is available
                if($Time_Out1 > $row->ClassroomOut){
                    $can_sched = false;
                }
                else{

                    // check if no subject tagged in the classroom
                    if(empty($subject_tagged_schedule)){

                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                b.STStatus = "Active" AND 
                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                ');

                        // check if no schedule of section on the same day
                        if(empty($section_subject_schedule_save)){
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
                                        $can_sched = false;
                                        //break;
                                        $and_di_pwede ='Professor is not Availablee';
                                        
                                    }
                                }// end of loop of the subject and schedule that this professor have     
                            }

                        }
                        else{
                            for($i = 0;$i < count($section_subject_schedule_save);$i++){
                                $j = $i;
                                // check if the generated start time is available in the professor schedule
                                if($request->Time_in1 >= $section_subject_schedule_save[$i]->STSTimeEnd){
                                    
                                    $j++;

                                    // check if the schedule subject of professor is empty
                                    if(!isset($section_subject_schedule_save[$j])){


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
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    $can_sched = false;
                                                    //break;
                                                    $and_di_pwede ='Professor is not Available';
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }


                                        // $can_sched = true;
                                        // break 1;
                                    }// end of check if the schedule subject of professor is empty
                                    else{
                                        if($Time_Out1 <= $section_subject_schedule_save[$j]->STSTimeStart){

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
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available';
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Professor is not Available';
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                            // $can_sched = true;
                                            // break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            // break;
                                            $and_di_pwede ='Section is not Available';
                                            
                                            
                                        }
                                    }
                                    
                                }// end of check if the generated start time is available in the professor schedule
                                else{
                                    $can_sched = false;
                                    //break;
                                    $and_di_pwede ='Section is not Available';
                                    
                                }
                            }// end of loop of the subject and schedule that this professor have     
                        }
                        
                    }   
                    else{
                        // loop of the subject that is scheduled in the classroom generated
                        for($i = 0;$i < count($subject_tagged_schedule);$i++){
                            $j = $i;

                            // check if the generated start time is available
                            if($request->Time_in1 >= $subject_tagged_schedule[$i]->STSTimeEnd){

                                $j++;

                                // check if the subject schedule in generate classroom is empty
                                if(!isset($subject_tagged_schedule[$j])){

                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                        FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                        WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                        a.STSDay = "'.$request->Day1.'" AND 
                                                                        b.STStatus = "Active" AND 
                                                                        a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                        ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
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
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';

                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    $can_sched = false;
                                                    //break;
                                                    $and_di_pwede ='Professor is not Available';
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


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
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                $can_sched = false;
                                                                //break;
                                                                $and_di_pwede ='Professor is not Available';
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }

                                                    // $can_sched = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

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
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Available';
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        // break;
                                                        $and_di_pwede ='Section is not Available';
                                        
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{
                                                $can_sched = false;
                                                //break;
                                                $and_di_pwede ='Section is not Available';
                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }


                                }// end of check if the subject schedule in generate classroom is empty
                                else{
                                    if($Time_out1 <= $subject_tagged_schedule[$j]->STSTimeStart){


                                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                            WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                            b.STStatus = "Active" AND 
                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                            ');

                                        // check if no schedule of section on the same day
                                        if(empty($section_subject_schedule_save)){
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
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available';

                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Professor is not Available';
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                        }
                                        else{
                                            for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                $jj = $ii;
                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($section_subject_schedule_save[$jj])){


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
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Available';
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

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
                                                                break 2;
                                                            }
                                                            else{
                                                                for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                    $jjj = $iii;

                                                                    // check if the generated start time is available in the professor schedule
                                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                        
                                                                        $jjj++;

                                                                        // check if the schedule subject of professor is empty
                                                                        if(!isset($professor_subject_schedule_save[$jjj])){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }// end of check if the schedule subject of professor is empty
                                                                        else{
                                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                                $can_sched = true;
                                                                                break 3;
                                                                            }
                                                                            else{
                                                                                $can_sched = false;
                                                                                // break;
                                                                                $and_di_pwede ='Professor is not Available';
                                                                            }
                                                                        }
                                                                        
                                                                    }// end of check if the generated start time is available in the professor schedule
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                        
                                                                    }
                                                                }// end of loop of the subject and schedule that this professor have     
                                                            }

                                                            // $can_sched = true;
                                                            // break 1;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Section is not Available';
                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    $can_sched = false;
                                                    //break;
                                                    $and_di_pwede ='Section is not Available';
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        $can_sched = false;
                                        $and_di_pwede = "No classroom is Available";
                                    }
                                }
                                
                            }// end of check if the generated start time is available
                            else{
                                $can_sched = false;
                                $and_di_pwede = "No Classroom is Available";
                            }
                        }// end loop of the subject that is scheduled in the classroom generated                        
                    }
                }

                if($can_sched == true){
                    $subject_tagging = DB::insert('
                        INSERT INTO subject_taggings (SubjectID,ProfessorID,SectionID,STSem,STYear,STYearFrom,STYearTo,STStatus,created_at,updated_at) VALUES
                                                ("'.$request['SubjectID'].'","'.$request['ProfessorID'].'",
                                                "'.$section_id.'","'.$request['STSem'].'","'.$request['STYear'].'",
                                                "'.$request['STYearFrom'].'","'.$request['STYearTo'].'",
                                                "'.$request['STStatus'].'",now(),now())
                        
                    ');

                    
                    $subject_taggings_id = DB::getPdo()->lastInsertId();              

                    DB::insert('INSERT INTO subject_tagging_schedules (STID,SMID,ClassroomID,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                    ("'.$subject_taggings_id.'","'.$request->smid1.'","'.$row->ClassroomID.'",
                    "'.$request->Time_in1.'","'.$Time_Out1.'","'.$request->Day1.'","Active",now(),now())');

                    break;
                }
            }
