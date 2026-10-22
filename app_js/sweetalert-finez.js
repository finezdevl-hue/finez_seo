function alertsuccess(text) {
    
    swal({

        title: "Success!",
        text: text,
        type: "success"
    });
}

function alerterror(obj, xhr) {
    var text = '';
    if (xhr.status == 401) {
        text = obj.Message;
    }
    else{
        text = obj.errmsg;
    }
    swal({
        title: "Error!",
        text:text,
        type: "warning"
    });
}
    function alertinfo(text) {
        swal({
            title: "Info!",
            text:text,
            type: "info"
        });
        }
        
   function alertwarning(text) {
            swal({
                title: "Warning!",
                text:text,
                type: "warning"
            });
            }


   //function getConfirmation() {
       
   //    swal({
   //        title: "Are you sure?",
   //        text: "Do you want save this data!",
   //        type: "warning",
   //        showCancelButton: true,
   //        confirmButtonColor: "#DD6B55",
   //        confirmButtonText: "Yes,Save!",
   //        closeOnConfirm: true
   //    }.then(function (isConfirm) {
   //        if (isConfirm) {
   //            return true;
   //        } else {
   //            return false;
   //        }
   //    })
   //    );
   //}


   function getSaveConfirmation() {
      alert("test1");
       swal({
           title: "Are you sure?",
           text: "Do you want to save this data!",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#DD6B55",
           confirmButtonText: "Yes,Save!",
           cancelButtonText: "Cancel",
           closeOnConfirm: true,
           closeOnCancel: true
       },
    function (isConfirm) {
        if (isConfirm) {
            return 1;
        } else {
            return 0;
        }
    }
);
}
   


