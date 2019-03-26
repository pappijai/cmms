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
                            <i class="fas fa-clock"></i> Schedule of Professors for {{$data['sem']}} Sy {{$data['year_from']}} - {{$data['year_to']}}
                        </h3>

                        <div class="card-tools">
                                <a class="btn btn-light mb-2 mr-2 text-teal" href="/">back</a>    
                        </div>                           
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="professor_schedule_table" class="table table-bordered table-hover">
                                <thead class="bgc-teal text-white">
                                    <tr class="text-center">
                                        <th width="40%">Professor Name</th>
                                        <th width="20%">action</th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    @if(!empty($data['professors']))
                                        @foreach ($data['professors'] as $item)
                                            <tr class="text-center">
                                                <td>{{$item->ProfessorName}}</td>
                                                <td>
                                                    <button class="btn btn-info text-white" 
                                                    onclick="view_schedule('{{$item->ProfessorID}}','{{$data['sem']}}','{{$data['year_from']}}','{{$data['year_to']}}')">
                                                        <li class="fas fa-eye"></li> Schedule
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                        @endforeach
                                    @endif 
                                </tbody>                         
                            </table>
                        </div>                        
                    </div>
                </div>            
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="subjects" tabindex="-1" role="dialog" aria-labelledby="taggedsubjectsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal_view">

            </div>
        </div>
    </div>    
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/datatables.net/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready( function () {
            $('#professor_schedule_table').DataTable();
        } );

        function view_schedule(id,sem,year_from,year_to){
            $.ajax({
                url: "/get_schedule_professor/"+id+"/"+sem+"/"+year_from+"/"+year_to,
                type: "GET",
                //data: {'type' : type},
                dataType: 'json',
                success: function(data){
                    $('#modal_view').html(data);
                    $('#subjects').modal('show');
                },
                error: function(){
                    alert('error');
                }                 
            });
            
        }
    </script>
</body>
</html>
