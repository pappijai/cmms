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
        <div class="mb-4" id="classrooms" v-show="show == 'classrooms'">
            <div class="card">
                <div class="card-header bgc-teal">
                    <h3 class="card-title text-white"><i class="fas fa-building"></i> {{$data['bldgname']}} - {{$data['floor_name']}}</h3>
                    <div class="card-tools">
                        <a class="btn btn-light mb-2 mr-2 text-teal" href="/get_floors/{{$data['bldgid']}}">back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <img src="/img/floorplan/{{$data['floor_photo']}}" usemap="#classroom" style="display: block;margin-left: auto;margin-right: auto;">
                            <map id="map_classroom_id" name="classroom">
                                @foreach ($data['floor_coordinates'] as $item)
                                    <area data-settings="{'fillColor': 'Thistle', 'fillOpacity': '1'}" 
                                                                    title="{{$item->ClassroomCode}}" 
                                                                    shape="rect" 
                                                                    id="{{$item->ClassroomID}}"
                                                                    href="/get_floors_classrooms_schedule/{{$item->ClassroomID}}/{{$item->BFID}}/{{$item->BFName}}/{{$data['floor_photo']}}/{{$data['bldgid']}}/{{$data['bldgname']}}/{{$item->ClassroomName}}" class="floorplan" 
                                                                    coords="{{$item->ClassroomCoordinates}}">                    
                                    
                                @endforeach
                            </map>                
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
