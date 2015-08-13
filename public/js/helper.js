function updateNode(id, data, success) {
    $.ajax({
        url: "/node/" + id,
        method: "PUT",
        data: data,
        success: success
    })
}

function deleteNode(id, success) {
    $.ajax({
        url: "/node/" + id,
        method: "DELETE",
        success: success
    })
}