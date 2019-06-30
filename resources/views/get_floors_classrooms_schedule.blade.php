<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">  
    <title>CMMS</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="shortcut icon" href="/img/favicon.png">
    <style>
        html, body {
            background-image: linear-gradient(-38deg, #08a4a7, #ffffff) !important;
        }        
    </style>  
</head>
<body class="hold-transition lockscreen">
    <div class="container mt-5">
        <div class="mb-4">
    
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bgc-teal">
                            <h3 class="card-title text-white"><i class="fas fa-building"></i> {{$data['classroom_name']}} Schedules</h3>
                            <div class="card-tools">
                                <a class="btn btn-light mb-2 mr-2 text-teal" href="/get_floor_classroom/{{$data['floor_id']}}/{{$data['floor_name']}}/{{$data['floor_photo']}}/{{$data['bldgid']}}/{{$data['bldgname']}}">back</a>
                                <a class="btn btn-success float-right mb-1" href="/print_schedule/{{$data['classroom_id']}}" target="_blank"><i class="fas fa-print"></i></a>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="classroom_schedule" class="table table-bordered table-hover">
                                    <thead class="bgc-teal text-white">
                                        <tr>
                                            <th scope="col">Monday</th>
                                            <th scope="col">Tuesday</th>
                                            <th scope="col">Wednesday</th>
                                            <th scope="col">Thursday</th>
                                            <th scope="col">Friday</th>
                                            <th scope="col">Saturday</th>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        @if(!empty($data['schedule']))
                                            @foreach ($data['schedule'] as $item)
                                                <tr>
                                                    <td>
                                                        @if($item->STSDay == 'Monday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Monday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Monday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Monday')
                                                                <p v-else-if="$item->STSDay == 'Monday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if($item->STSDay == 'Tuesday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Tuesday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Tuesday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Tuesday')
                                                                <p v-else-if="$item->STSDay == 'Tuesday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
    
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if($item->STSDay == 'Wednesday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Wednesday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Wednesday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Wednesday')
                                                                <p v-else-if="$item->STSDay == 'Wednesday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
    
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if($item->STSDay == 'Thursday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
    
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Thursday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Thursday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Thursday')
                                                                <p v-else-if="$item->STSDay == 'Thursday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
    
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->STSDay == 'Friday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Friday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Friday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Friday')
                                                                <p v-else-if="$item->STSDay == 'Friday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
    
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if($item->STSDay == 'Saturday')
                                                            <p>
                                                                {{$item->SubjectDescription}}<br/>
                                                                {{$item->Schedule}}<br/>
                                                                ({{$item->ProfessorName}})
                                                            </p>
                                                            @if($data['month_today'] >= 5 && $data['month_today'] <= 9 && $item->STSTDay = 'Saturday')
                                                                <p v-if="month_today >= 5 && month_today <= 9 && $item->STSDay == 'Saturday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear + 1}} - {{$item->SectionName}}
                                                                </p>
                                                            @elseif($item->STSDay == 'Saturday')
                                                                <p v-else-if="$item->STSDay == 'Saturday'">
                                                                    {{$item->CourseCode}} {{$data['year_today'] - $item->SectionYear}} - {{$item->SectionName}}
                                                                </p>
                                                            @else
    
                                                            @endif
                                                        @endif

                                                                                
                                                    </td>
                                                </tr>
                                                
                                            @endforeach
                                        
                                        @endif 
                                    </tbody>                         
                                </table>
                            </div>

                        </div>
                    </div>
                    <!-- <button type="button" class="btn btn-success float-right mb-1" @click="print_schedule(classroomid)"><i class="fas fa-print"></i></button> -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
