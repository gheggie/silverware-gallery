(function($){
    
    $.entwine('ss.tree', function($) {
        
        // Tree Node Refresh Handler:
        
        $('input.hidden.cms-tree-refresh').entwine({
            
            onmatch: function() {
                var id = this.val();
                var tree = $('.cms-tree');
                var node = tree.getNodeByID(id);
                tree.jstree('refresh', node, function () {
                    tree.jstree('open_node', node);
                });
            }
            
        });
        
    });
    
}(jQuery));
