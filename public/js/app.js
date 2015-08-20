//Process the nodes
var rootNode = nodes[0];
rootNode.level = 0;
var currentTarget = null;

function processNode(currentNode, nodes) {
    currentNode.children = [];
    currentNode.targeting = false;
    for (var i = 0; i < nodes.length; i++) {
        var node = nodes[i];
        if (node.parent_id == currentNode.id) {
            node.level = currentNode.level + 1;
            currentNode.children.push(node);
            processNode(node, nodes);
        }
    };
    //Sort by weight
    currentNode.children = _.sortBy(currentNode.children, "weight");
    //Set up prev and next
    var length = currentNode.children.length;
    for (var i = 0; i < length; i++) {
        var child = currentNode.children[i];
        child.prev = i - 1 >= 0 ? currentNode.children[i - 1] : null;
        child.next = i + 1 < length ? currentNode.children[i + 1] : null;
    };
}
processNode(rootNode, nodes);

// define the node component
Vue.component('node', {
    props: ['model'],
    template: '#node-template',
    replace: true,
    computed: {
        numChildren: function() {
            if (!this.model.children || this.model.children.length == 0)
                return 0;
            return this.model.children.length;
        },
        hasChildren: function() {
            return this.numChildren > 0;
        },
        isLeaf: function() {
            return this.numChildren == 0;
        },
        isRoot: function() {
            return this.model.parent_id == 0;
        },
        isTargeting: function() {
            return this.model.targeting;
        },
        completed: function() {
            var model = this.model;
            if (this.isLeaf)
                return model.completion;
            var total = 0;
            for (var i in this.$children) {
                var child = this.$children[i];
                total += child.completed;
            }
            return total / model.children.length;
        },
        dimension: function() {
            //First take care of the root node
            if (this.model.parent_id == 0) {
                return "full";
            }

            var containerWidth = this.$parent.$el.scrollWidth;
            var containerHeight = this.$parent.$el.scrollHeight;
            var numSiblings = this.$parent.numChildren;

            switch (numSiblings) {
                case 2:
                    if (containerWidth > containerHeight) {
                        return "hori-2";
                    }
                    return "vert-2";
                case 3:
                    if (containerWidth > containerHeight) {
                        return "hori-3";
                    }
                    return "vert-3";
                case 4:
                    if (containerWidth >= containerHeight * 3.0) {
                        return "hori-4";
                    } else if (containerWidth >= containerHeight) {
                        return "square-4";
                    }
                    return "vert-4";
            }
            return "full";
        },
        size: function() {
        	var aa = this.$;
        	var bb = this.$$;
        	this.model.name;
        	//var numSiblings = this.$parent.numChildren;
            if (this.isLeaf) {
                return Math.min(this.$el.scrollWidth, this.$el.scrollHeight);
            }
            return 0;
        }
    },
    methods: {
        add: function() {
        	var model = this.model;
            var name = prompt("Enter node name");
            if (!name)
                return;
            newNode = {
                name: name,
                project: project,
                parent_id: this.model.id
            };
            //Store it
            $.post("/node", newNode, function(data) {
                //model.children.push(data);
                nodes.push(data);
                processNode(rootNode, nodes);
            }, "json");
        },
        edit: function() {
        	editModalData.node = this.model;
        	editModalData.newName = this.model.name;
        	editModalData.newCompletion = this.model.completion * 100;
        	editModalData.newBody = this.model.body;
            $('#editModal').openModal({
            	ready: function(){
            		$("#name").focus();
            	}
            });
        },
        editName: function() {
            var newName = prompt("Enter a new name");
            var model = this.model;
            if (!newName)
                return;
            updateNode(model.id, {
                name: newName
            }, function() {
                model.name = newName;
            });
        },
        delete: function() {
            if (confirm("Are you sure?")) {
                var id = this.model.id;
                deleteNode(id, function() {
                    nodes = _.reject(nodes, function(node) {
                        return node.id == id;
                    })
                    processNode(rootNode, nodes);
                });
            }
        },
        //Move current node to be infront of previous node
        move: function(direction) {
            var model = this.model;
            if (model[direction] == null)
                return;
            var newWeight = model.weight;
            var dirWeight = model[direction].weight;
            if (model[direction][direction] == null) {
                newWeight = dirWeight + (direction == "prev" ? -1.0 : 1.0);
            } else {
                var dirDirWeight = model[direction][direction].weight;
                newWeight = (dirWeight + dirDirWeight) / 2.0;
            }
            updateNode(model.id, {
                weight: newWeight
            }, function() {
                model.weight = newWeight;
                processNode(rootNode, nodes);
            });
        },
        target: function(e) {
            e.stopPropagation()
            var model = this.model;

            clearTarget();
            model.targeting = true;
            currentTarget = model;
        },
        cardClick: function(e) {
            e.stopPropagation()
            if (currentTarget == null)
                return;
            var model = this.model;
            var target = currentTarget;
            updateNode(target.id, {
                parent_id: model.id
            }, function() {
                target.parent_id = model.id;
                processNode(rootNode, nodes);
            });

            clearTarget();
        }
    }
})

// boot up the canvas
var canvas = new Vue({
    el: '#canvas',
    data: {
        treeData: rootNode
    },
    events: {
        'hook:ready': function() {
            //force the view to update (this is a fixup)
            processNode(rootNode, nodes);
        },
        "hook:attached": function() {
        	console.log("gg")
        }
    }
})


/*
 * jQuery events
 */
$(document).keyup(function(e) {
    if (e.keyCode == 27) { // escape key maps to keycode `27`
        clearTarget();
    }
});

function clearTarget() {
    if (currentTarget != null)
        currentTarget.targeting = false;
    currentTarget = null;
}

/*
 * Vue for edit modal
 */
var editModalData = {
    node: null,
    newName: null,
    newCompletion: null,
    newBody: null,
};
var editModal = new Vue({
    el: '#editModal',
    data: editModalData,
    methods: {
    	submit: function() {
    		var node = this.node;
    		var id = this.node.id;
    		var newName = this.newName;
    		var newCompletion = this.newCompletion / 100;
    		var newBody = this.newBody;
    		updateNode(id, {
                name: newName,
                completion: newCompletion,
                body: newBody,
            }, function() {
                node.name = newName;
                node.completion = newCompletion;
                node.body = newBody;
                window.location.reload(true);
            });
    		$("#editModal").closeModal();
    	}
    }
})
