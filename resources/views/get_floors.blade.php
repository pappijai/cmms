<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">  
    <title>CMMS</title>
      <link rel="stylesheet" href="/css/app.css">
      <link rel="shortcut icon" href="../img/favicon.png">
      <style>
        html, body {
            background-image: linear-gradient(-38deg, #08a4a7, #ffffff) !important;
        }        
    </style>  
</head>
<body class="hold-transition lockscreen">
    <div class="container mt-5">
        <div class="mb-4" id="floors">
            <div class="card">
                <div class="card-header bgc-teal">
                    <h3 class="card-title text-white"><i class="fas fa-building"></i> {{$data['bldgName']}}</h3>
                    <div class="card-tools">
                        <a class="btn btn-light mb-2 mr-2 text-teal" href="/view_floorplan">back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        @foreach ($data['floors'] as $item)
                            <div class="col">
                                <div class="text-center">
                                    <h5>{{$item->BFName}}</h5>
                                    <a href="/get_floor_classroom/{{$item->BFID}}/{{$item->BFName}}/{{$item->BFPhoto}}/{{$data['bldgid']}}/{{$item->BldgName}}">
                                        <img src="/img/floorplan/{{$item->BFPhoto}}" width="100%" height="100%" class="rounded" alt="...">
                                    </a>
                                </div>                
                            </div>
                            
                        @endforeach
                    </div>

                </div>
            </div>
                
        </div>
    </div>
</body>
</html>
