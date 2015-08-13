<!DOCTYPE html>
<html>

<head>
    <title>{{$project}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/project.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/vue/0.12.10/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script>
    /* beautify preserve:start */
    var project = {!! json_encode($project) !!};
    var nodes = {!!json_encode($nodes, JSON_NUMERIC_CHECK) !!};
    /* beautify preserve:end */
    </script>
</head>

<body class="grey">
    <script type="text/x-template" id="node-template">
        <div class="@{{dimension}}">
            <div class="card level-@{{model.level}}" v-on="click: cardClick">
                <div class="header darken-4 white-text" v-class="leaf-red: isLeaf, blue-grey: !isLeaf, selected: model.targeting">
                    <div class="valign-wrapper">
                        <div class="valign center-align">@{{model.name}} @{{parseInt(completed * 100)}}%</div>
                        <div class="controls center-align">
                            <a v-on="click: move('prev')" v-show="!isRoot" class="white-text left"><i class="fa fa-chevron-circle-left"></i></a>
                            <a v-on="click: target" v-show="!isRoot" class="white-text"><i class="fa fa-crosshairs"></i></a>
                            <a v-on="click: edit" v-show="!isRoot" class="edit white-text"><i class="fa fa-pencil-square"></i></a>
                            <a v-on="click: add" class="add white-text"><i class="fa fa-plus-circle"></i></a>
                            <a v-on="click: delete" v-show="!isRoot" class="delete white-text"><i class="fa fa-minus-square"></i></a>
                            <a v-on="click: move('next')" v-show="!isRoot" class="white-text right"><i class="fa fa-chevron-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="body">
                    <div class="valign-wrapper full" v-show="isLeaf">
                        <div class="valign center" style="width:100%;font-size: @{{size/2.4}}px;">@{{parseInt(model.completion * 100)}}%</div>
                    </div>
                    <node class="node" v-repeat="model: model.children"></node>
                </div>
            </div>
        </div>
    </script>
    <div id="canvas" class="">
        <node class="node" model="@{{treeData}}"></node>
    </div>
    <!-- Modal Structure -->
    <div id="editModal" class="modal">
        <div class="modal-content" v-show="node">
            <div class="row">
                <div class="input-field col s12">
                    <input id="name" type="text" v-on="keyup:submit | key 'enter'" v-model="newName">
                    <label for="name">Name</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m2">
                    <input type="number" id="completion" min="0" max="100" v-on="keyup:submit | key 'enter'" v-model="newCompletion" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="waves-effect waves-green btn-flat" v-on="click: submit">Save</a>
        </div>
    </div>
    <script src="/js/helper.js"></script>
    <script src="/js/app.js"></script>
</body>

</html>
