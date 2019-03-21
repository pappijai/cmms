<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">  
    <title>CMMS</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="shortcut icon" href="./img/favicon.png">
    <style>
        html, body {
            background-image: linear-gradient(-38deg, #08a4a7, #ffffff) !important;
        }        
    </style>  
</head>
<body class="hold-transition lockscreen">
    <div class="container mt-5">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white" align="center">
                            <i class="fas fa-map-marked"></i>
                            @foreach ($data['floorplan'] as $item)
                                {{$item->FloorplanName}} Floorplan
                                
                            @endforeach
                        </h3>
                        <div class="card-tools">
                                <a class="btn btn-light mb-2 mr-2 text-teal" href="/">back</a>    
                        </div>                           
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="table table-responsive">
                                @foreach ($data['floorplan'] as $item)
                                    <img src="./img/floorplan/{{$item->FloorplanPhoto}}" usemap="#floor" style="display: block;margin-left: auto;margin-right: auto;">
                                @endforeach
                                <map id="map_id" name="floor">
                                    @foreach ($data['building'] as $item)
                                        <area data-settings="{'fillColor': 'Thistle', 'fillOpacity': '1'}" 
                                                shape="rect" class="floorplan"
                                                title="{{$item->BldgName}}" 
                                                coords="{{$item->BldgCoordinates}}"
                                                id="{{$item->BldgID}}"
                                                href="/get_floors/{{$item->BldgID}}">
                                        
                                    @endforeach
                                </map>                
                            </div>
                        </div>    
                    </div>
                </div>            
            </div>
        </div>
    </div>
{{-- <script src="/js/app.js"></script> --}}
</body>
</html>
