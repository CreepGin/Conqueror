<!DOCTYPE html>
<html>

<head>
    <title>Conqueror</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
    <style>
    html,
    body {
        height: 100%;
    }
    
    body {
        margin: 0;
        padding: 0;
        width: 100%;
    }
    
    #content {
        width: 500px;
        margin: 100px auto;
    }
    </style>
</head>

<body>
    <div id="content">
        <table>
            <thead>
                <tr>
                    <th data-field="id">Name</th>
                    <th data-field="name">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                <tr>
                    <td><a href="/{{$project}}">{{$project}}</a></td>
                    <td><a class="btn delete red" data-project="{{$project}}">Delete</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
    $(".btn.delete").click(function(){
        if (confirm("Ary you sure?")) {
            var project = $(this).attr("data-project");
            $.ajax({
                url: "/delete/"+project,
                method: "get",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(){
                    document.location.reload(true);
                }
            });
        }
    });
    </script>
</body>

</html>
