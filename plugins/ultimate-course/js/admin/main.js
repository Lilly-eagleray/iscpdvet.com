jQuery(document).ready(function( $ ) {
	$(".open_edit_email").click(function(e){
		e.preventDefault();
		var email_list = $("#customer_email").val();
		
		$(".email_input").html(email_list);
		$('#email_modal').modal('show');

});

$("#sortable").sortable({update: function(event, ui) {
            var data = {
                        action: 'update_order',
                stringval: JSON.stringify($("#sortable").sortable('toArray')),
				updateWpml :$(".update_all_lang").val()
              };
              $.post(ajax_object.ajax_url, data, function(response) {
                  
                    console.log(response);
                  
              });

        }
		
		});


$(".save_emails").click(function(e){
		e.preventDefault();
		var email_list = $(".email_input").val();
		
		$("#customer_email").val(email_list);
		$('#email_modal').modal('hide');

});


	$(".user_id").customselect();
	$(".product_id").customselect();
	
	$( ".box_admin" ).click(function() {
		$( ".box_admin" ).removeClass("active");
		$(this).addClass("active");
		$(".box_big").hide();
		$("#" + $(this).data("box")).show();
	});
	
	
	
	
	 $('.type_lesson').change(function() {
		$(".type_lesson_box").hide();
        if (this.value == 1) {
           $(".video_type").show();
        }
		else if (this.value == 2) {
            $(".video_type_local").show();
        }
        else if (this.value == 3) {
            $(".pdf_type").show();
        }
		  else if (this.value == 4) {
            $(".ppt_type").show();
        }
		  else if (this.value == 5) {
            $(".iplayer_type").show();
        }
		  else if (this.value == 6) {
            $(".iplayer_playlist_type").show();
        }
		
		
    });
	
	
	
	
	$.wpMediaUploader();
	

	 var t = $('.user-table').DataTable({
				"language":	{
				"decimal":        "",
				"emptyTable":     "אין מידע להצגה בטבלה",
				"info":           "מציג _START_ עד _END_ מתוך _TOTAL_ רשומות",
				"infoEmpty":      "מציג 0 עד 0 מתוך 0 רשומות",
				"infoFiltered":   "(מסנן מתוך _MAX_ כל הרשומות)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     " מציג _MENU_ רשומות",
				"loadingRecords": "טוען...",
				"processing":     "מעבד...",
				"search":         "חיפוש:",
				"zeroRecords":    "לא נמצאו רשומות",
				"paginate": {
					"first":      "ראשון",
					"last":       "אחרון",
					"next":       "הבא",
					"previous":   "הקודם"
				},
				"aria": {
					"sortAscending":  ": activate to sort column ascending",
					"sortDescending": ": activate to sort column descending"
				}
			}
	
});
	
	 var t = $('.user-table-course').DataTable({
				"language":	{
				"decimal":        "",
				"emptyTable":     "אין מידע להצגה בטבלה",
				"info":           "מציג _START_ עד _END_ מתוך _TOTAL_ רשומות",
				"infoEmpty":      "מציג 0 עד 0 מתוך 0 רשומות",
				"infoFiltered":   "(מסנן מתוך _MAX_ כל הרשומות)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     " מציג _MENU_ רשומות",
				"loadingRecords": "טוען...",
				"processing":     "מעבד...",
				"search":         "חיפוש:",
				"zeroRecords":    "לא נמצאו רשומות",
				"paginate": {
					"first":      "ראשון",
					"last":       "אחרון",
					"next":       "הבא",
					"previous":   "הקודם"
				},
				"aria": {
					"sortAscending":  ": activate to sort column ascending",
					"sortDescending": ": activate to sort column descending"
				}
			},
		
		  "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]
	});
	t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $(".select-t").select2();

	
});


