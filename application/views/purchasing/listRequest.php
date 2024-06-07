<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#btnSaveDetail").click(function() {
            var idReq = $("#txtIdReq").val();
            var cekFile = "";
            var formData = new FormData();

            var idDetail = "";
            var valIdDetail = $("input[name^='txtIdReqDetail']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valIdDetail.length; l++) {
                if (idDetail == "") {
                    idDetail = valIdDetail[l];
                } else {
                    idDetail += "*" + valIdDetail[l];
                }

                var cekFileUpload = $("#uploadFile_" + valIdDetail[l]).val();

                if (!cekFileUpload) {
                    cekFileUpload = "-";
                }
                if (cekFile == "") {
                    cekFile = cekFileUpload;
                } else {
                    cekFile += "*" + cekFileUpload;
                }

                var fileUpload = $("#uploadFile_" + valIdDetail[l]).prop('files')[0];

                formData.append('uploadFile_' + valIdDetail[l], fileUpload);
            }
            var ttlApprove = "";
            var valTtlApprove = $("input[name^='txtTtlApprove']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valTtlApprove.length; l++) {
                if (ttlApprove == "") {
                    ttlApprove = valTtlApprove[l];
                } else {
                    ttlApprove += "*" + valTtlApprove[l];
                }
            }

            var remark = "";
            var valRemark = $("textarea[name^='txtRemark']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valRemark.length; l++) {
                if (valRemark[l] == "") {
                    valRemark[l] = "-";
                }
                if (remark == "") {
                    remark = valRemark[l];
                } else {
                    remark += "*" + valRemark[l];
                }
            }

            formData.append('idReq', idReq);
            formData.append('idDetail', idDetail);
            formData.append('ttlApprove', ttlApprove);
            formData.append('remark', remark);
            formData.append('cekFile', cekFile);

            $("#idLoading").show();
            $(this).attr("disabled", true);

            $.ajax("<?php echo base_url('listRequest/addRequestDetail'); ?>", {
                method: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert(response);
                    reloadPage();
                }
            });

        });
        $("#btnSearch").click(function() {
            var idSlcType = $("#idSlcType").val();
            var valSearch = $("#txtSearch").val();

            if (valSearch == "") {
                alert("Search Empty..!!");
                return false;
            }
            $("#idLoading").show();
            $.post('<?php echo base_url("listRequest/getListRequest"); ?>/search/', {
                    valSearch: valSearch,
                    idSlcType: idSlcType
                },
                function(data) {
                    $("#idTbody").empty();
                    $("#idTbody").append(data.trNya);
                    $("#idPage").empty();
                    $("#idLoading").hide();
                },
                "json"
            );
        });
    });

    $(document).ready(function() {

        function resetForm() {
            $("#detailContainer").find("input[type='text'], textarea").val("");
            $("#detailContainer").find("select").prop('selectedIndex', 0);
            $("#detailContainer").find("input[type='file']").val("");
        }

        $("#btnSavePurchaseDetail").click(function() {
            var idReq = $("#txtIdReq").val();
            var formData = new FormData();
            var idEdit = "";
            var valIdEdit = $("input[name^='txtIdEdit']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valIdEdit.length; l++) {
                if (valIdEdit[l] == "") {
                    valIdEdit[l] = "-";
                }
                if (idEdit == "") {
                    idEdit = valIdEdit[l];
                } else {
                    idEdit += "*" + valIdEdit[l];
                }
            }

            formData.append('id', idEdit);
            formData.append('idReq', idReq);

            function appendFormData(fieldName, selector) {
                var values = $(selector).map(function() {
                    return $(this).val();
                }).get();
                formData.append(fieldName, values.join('*'));
            }

            appendFormData('codeNo', "input[name^='txtCodePartNoModal']");
            appendFormData('nameArtikel', "input[name^='txtNameArticleModal']");
            appendFormData('unit', "input[name^='txtUnitModal']");
            appendFormData('working', "input[name^='txtWorkOnBoardModal']");
            appendFormData('stock', "input[name^='txtStockOnBoardModal']");
            appendFormData('request', "input[name^='txtTotalReqModal']");
            appendFormData('approved_order', "input[name^='txtTotalApproveModal']");

            var marks = $("select[name^='slcMarkDetail']").map(function() {
                return $(this).val() || '-';
            }).get();
            formData.append('mark', marks.join('*'));

            var request_remark = $("textarea[name^='txtModalRemark']").map(function() {
                return $(this).val() || '-';
            }).get();
            formData.append('request_remark', request_remark.join('*'));

            $("input[type^='file']").each(function(index, input) {
                if (input.files.length > 0) {
                    formData.append(input.name, input.files[0]);
                }
            });

            if (formData.get('codeNo') === "" || formData.get('nameArtikel') === "" || formData.get(
                    'unit') === "") {
                alert("Code / Part No, Name of Article, and Unit cannot be empty!");
                return false;
            }

            $("#idLoading").show();

            $.ajax({
                url: '<?php echo base_url("listRequest/addPurchasingDetail"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    alert(data);
                    editData(idReq);
                    checkReq(idReq);
                    $("#modalEditDetail").modal('hide');
                    resetForm(); // Membersihkan formulir sebelum mengumpulkan data baru
                },
                dataType: 'json'
            });
        });
    });

    $(document).ready(function() {
        function updateButtonVisibility() {
            $('.btnRemoveRow').hide();
            if ($('.detailRow').length > 1) {
                $('.btnRemoveRow').show();
            }
        }
        $('#detailContainer').on('click', '.btnAddRow', function() {
            var $clone = $(this).closest('.detailRow').clone();
            $clone.find('input').val('');
            $clone.find('textarea').val('');
            $clone.find('select').val('');
            $('#detailContainer').append($clone);
            updateButtonVisibility();
        });
        $('#detailContainer').on('click', '.btnRemoveRow', function() {
            if ($('.detailRow').length > 1) {
                $(this).closest('.detailRow').remove();
                updateButtonVisibility();
            }
        });

        updateButtonVisibility();
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('btnCancelDetail').addEventListener('click', function() {
            var modal = document.getElementById('modalEditDetail');
            var bsModal = bootstrap.Modal.getInstance(modal);
            bsModal.hide();
        });
    });

    function delData(id) {
        var cfm = confirm("Yakin Hapus..??");
        if (cfm) {
            $.post('<?php echo base_url("listRequest/delData"); ?>', {
                    id: id,
                    typeDel: "delPur"
                },
                function(response) {
                    if (response === "Delete Success..!!") {
                        alert("Data berhasil dihapus!");
                        $("#row_" + id).remove(); // Menghapus baris dari tabel
                    } else {
                        alert("Gagal menghapus data: " + response);
                    }
                },
                "json"
            );
        }
    }


    function editData(id) {
        $("#idLoading").show();
        $("#lblForm").text("Edit Data");

        $.post('<?php echo base_url("listRequest/editData"); ?>', {
                id: id,
                typeEdit: "editCheckReq"
            },
            function(data) {
                $("#idDataTable").hide();
                $("#txtIdReq").val(data.idReq);

                $.each(data.headNya, function(i, item) {
                    $("#idLblApp").text(item.app_no);
                    $("#idLblVessel").text(item.vessel);
                    $("#idLblDept").text(item.department);
                });

                $("#idLblReqDate").text(data.reqDate);

                // Bersihkan tabel sebelum menambahkan data baru
                $("#idTbodyDetail").html(''); // Gunakan html() untuk mengosongkan tabel
                $("#idTbodyDetail").append(data.trNya);

                $("#idLoading").hide();
                $("#idFormDetail").show(200);
            },
            "json"
        );
    }

    function submitData(idReq = '') {
        var cfm = confirm("Yakin di Submit..??");

        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("listRequest/submitData"); ?>/', {
                    id: idReq
                },
                function(data) {
                    alert(data);
                    reloadPage();
                },
                "json"
            );
        }
    }

    function exportData(idReq = '') {
        window.open('<?php echo base_url('listRequest/exportDataReq');?>' + '/' + idReq, '_blank');
    }

    function exportDataView(idReq = '') {
        window.open('<?php echo base_url('listRequest/exportDataReqView');?>' + '/' + idReq, '_blank');
    }

    function showModalCancel(id) {
        var idReq = id;
        $("#idTableModal").empty();
        $("#lblModal").text("Cancel Data");
        var divNya = "";
        divNya += "<div class='row'>";
        divNya += "<div class='col-md-12'>";
        divNya += "<div class='col-md-2'>";
        divNya += "<label>Remark :</label>";
        divNya += "</div>";
        divNya += "<div class='col-md-10'>";
        divNya += "<textarea class='form-control input-sm' id='txtCancelModal'></textarea>";
        divNya += "</div>";
        divNya += "</div>";
        divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
        divNya += "<button id='btnSubmitModalReq' onclick='cancelReq(" + idReq +
            ");' class='btn btn-primary btn-sm' title='Cancel'><i class='fa fa-check-square-o'></i> Submit</button> <button id='btnCancelModalReq' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
        divNya += "</div>";
        divNya += "</div>";
        $("#idTableModal").append(divNya);
        $('#modalReqDetail').modal('show');
    }

    function showModalRevise(id) {
        var idReq = id;
        $("#idTableModal").empty();
        $("#lblModal").text("Revise Data");
        var divNya = "";
        divNya += "<div class='row'>";
        divNya += "<div class='col-md-12'>";
        divNya += "<div class='col-md-2'>";
        divNya += "<label>Remark :</label>";
        divNya += "</div>";
        divNya += "<div class='col-md-10'>";
        divNya += "<textarea class='form-control input-sm' id='txtReviseModal'></textarea>";
        divNya += "</div>";
        divNya += "</div>";
        divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
        divNya += "<button id='btnSubmitModalReq' onclick='reviseReq(" + idReq +
            ");' class='btn btn-primary btn-sm' title='Revise'><i class='fa fa-check-square-o'></i> Submit</button> <button id='btnCancelModalReq' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
        divNya += "</div>";
        divNya += "</div>";
        $("#idTableModal").append(divNya);
        $('#modalReqDetail').modal('show');
    }

    function cancelReq(id) {
        var idReq = id;
        var remark = $("#txtCancelModal").val();

        if (remark == "") {
            alert("Remark cant be blanked..!!");
            return false;
        }

        var cfm = confirm("Submit data Cancel..??");
        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("listRequest/submitCancel"); ?>/', {
                    idReq: idReq,
                    remark: remark
                },
                function(data) {
                    reloadPage();
                },
                "json"
            );
        }
    }

    function reviseReq(id) {
        var idReq = id;
        var remark = $("#txtReviseModal").val();

        var cfm = confirm("Submit data Revise..??");
        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("listRequest/submitRevise"); ?>/', {
                    idReq: idReq,
                    remark: remark
                },
                function(data) {
                    reloadPage();
                },
                "json"
            );
        }
    }

    function checkReq(id) {
        $("#idLoading").show();
        $.post('<?php echo base_url("listRequest/editData"); ?>', {
                id: id,
                typeEdit: "checkReq"
            },
            function(data) {
                $("#idDataTable").hide();
                $("#txtIdReq").val(data.idReq);
                $.each(data.headNya, function(i, item) {
                    $("#idLblApp").text(item.app_no);
                    $("#idLblVessel").text(item.vessel);
                    $("#idLblDept").text(item.department);
                });
                $("#idLblReqDate").text(data.reqDate);

                // Bersihkan tabel sebelum menambahkan data baru
                $("#idTbodyDetail").html(''); // Gunakan html() untuk mengosongkan tabel
                $("#idTbodyDetail").append(data.trNya);

                $("#idLoading").hide();
                $("#idFormDetail").show(200);
            },
            "json"
        );
    }

    function showModal(id) {
        $("#idLoading").show();
        $("#lblModal").text("View Data");
        $("#btnExportPdfView").attr("onclick", "");
        $("#btnExportPdfView").attr("onclick", "exportDataView('" + "" + id + "" + "');");

        $.post('<?php echo base_url("listRequest/getModalDetailReq"); ?>', {
                idReq: id
            },
            function(data) {
                $("#idTbodyDetailReq").empty();
                $("#idTbodyDetailReq").append(data.trNya);
                $("#lblModalStatus").text(data.stData);
                $("#idLoading").hide();
                $('#modalReqDetail').modal('show');
            },
            "json"
        );
    }

    function clearFile(idClear) {
        $("#" + idClear).val("");
    }

    function delFile(idDel, nmFile) {
        var cfm = confirm("Delete File..??");
        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("listRequest/editData"); ?>', {
                    id: idDel,
                    typeEdit: "delFile",
                    nmFile: nmFile
                },
                function(data) {
                    alert(data.stData);
                    $("#idLoading").hide();
                    $("#idLinkFile_" + idDel).empty();
                },
                "json"
            );
        }
    }

    function viewRevisi(id) {
        var idReq = id;
        $("#idLoading").show();
        $("#lblModal").text("View Revisi");

        $.post('<?php echo base_url("listRequest/viewRevisi"); ?>', {
                id: idReq
            },
            function(data) {
                $("#idTableModal").empty();
                $("#idTableModal").css("min-height", "200px");
                $("#idTableModal").append(data.divNya);
                $('#modalReqDetail').modal('show');
                $("#idLoading").hide();
            },
            "json"
        );
    }

    function reloadPage() {
        window.location = "<?php echo base_url('listRequest/getListRequest');?>";
    }
    </script>
</head>

<body>
    <section id="container">
        <section id="main-content">
            <section class="wrapper site-min-height" style="min-height:400px;">
                <h3>
                    <i class="fa fa-angle-right"></i> PR List<span style="display:none;padding-left:20px;"
                        id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>"></span>
                </h3>
                <div class="form-panel" id="idDataTable">
                    <div class="row" id="btnNavAtas">
                        <div class="col-md-2">
                            <select class="form-control input-sm" id="idSlcType">
                                <option value="appNo">App No</option>
                                <option value="vessel">Vessel</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="txtSearch" value=""
                                placeholder="Search Text" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnSearch" class="btn btn-warning btn-sm btn-block" title="Add"><i
                                    class="fa fa-search"></i> Search</button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="reloadPage();" id="btnSearch"
                                class="btn btn-success btn-sm btn-block" title="Add"><i class="fa fa-refresh"></i>
                                Refresh</button>
                        </div>
                    </div>
                    <div class="row mt" id="idData1">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                    <thead>
                                        <tr style="background-color: #A70000;color: #FFF;height:40px;">
                                            <th style="vertical-align: middle; width:5%;text-align:center;" colspan="2">
                                                No</th>
                                            <th style="vertical-align: middle; width:10%;text-align:center;">Req. Date
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">App. No
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Vessel</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Department
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Status</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Required
                                            </th>
                                            <th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idTbody">
                                        <?php echo $trNya; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="idPage"><?php echo $listPage; ?></div>
                        </div>
                    </div>
                </div>
                <div class="form-panel" id="idFormDetail" style="display: none;">
                    <legend><label id="lblForm"> Approval Data</label></legend>
                    <div class="row">
                        <div class="col-md-2 col-xs-12">
                            <label>Request Date :</label>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <label id="idLblReqDate"></label>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <label>Vessel :</label>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <label id="idLblVessel"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-12">
                            <label>App. No :</label>
                        </div>
                        <div class="col-m  d-4 col-xs-12">
                            <label id="idLblApp"></label>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <label>Department :</label>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <label id="idLblDept"></label>
                        </div>
                    </div>
                    <!-- Button for opening modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg"
                        style="margin-bottom: 15px;">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                    <div class="modal fade bd-example-modal-lg" role="dialog" id="modalEditDetail">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="padding: 10px; background-color: #A70000;">
                                    <h4 class="modal-title">Edit Purchasing Detail</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-12">
                                        <!-- Container for dynamically added rows -->
                                        <div id="detailContainer">
                                            <!-- Initial row template -->
                                            <div class="row detailRow">
                                                <input type="hidden" name="txtIdEdit[]" class="txtIdEdit" value="">
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtCodePartNoModal" id="lblForm"><u>Part
                                                                No:</u></label>
                                                        <input placeholder="Code / Part No" type="text"
                                                            class="form-control input-sm" name="txtCodePartNoModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtNameArticleModal" id="lblForm"><u>Name
                                                                Article:</u></label>
                                                        <input placeholder="Name of Article" type="text"
                                                            class="form-control input-sm" name="txtNameArticleModal[]"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtUnitModal" id="lblForm"><u>Unit:</u></label>
                                                        <input placeholder="Unit" type="text"
                                                            class="form-control input-sm" name="txtUnitModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtWorkOnBoardModal"
                                                            id="lblForm"><u>Working:</u></label>
                                                        <input placeholder="Working" type="text"
                                                            class="form-control input-sm" name="txtWorkOnBoardModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtStockOnBoardModal"
                                                            id="lblForm"><u>Stock:</u></label>
                                                        <input placeholder="Stock" type="text"
                                                            class="form-control input-sm" name="txtStockOnBoardModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtTotalReqModal"
                                                            id="lblForm"><u>Request:</u></label>
                                                        <input placeholder="Request" type="text"
                                                            class="form-control input-sm" name="txtTotalReqModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtTotalApproveModal" id="lblForm"><u>Total
                                                                Approve:</u></label>
                                                        <input placeholder="Total Approve" type="text"
                                                            class="form-control input-sm" name="txtTotalApproveModal[]"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="slcMarkDetailModal" id="lblForm"><u>Mark
                                                                Reference:</u></label>
                                                        <select name="slcMarkDetail[]" class="form-control input-sm">
                                                            <option value="">- Select -</option>
                                                            <option value="A">TO BE REPLACE URGENTLY</option>
                                                            <option value="B">BETTER TO BE REPLACE</option>
                                                            <option value="C">STOCK FOR NEXT O/HAUL</option>
                                                            <option value="D">STOCK FOR EMERGENCY</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="txtRemarkModal" id="lblForm"><u>Remark:</u></label>
                                                        <textarea name="txtModalRemark[]" class="form-control input-sm"
                                                            required></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-xs-10">
                                                    <div class="form-group">
                                                        <label for="uploadFilePurchase0" id="lblForm"><u>Upload
                                                                File:</u></label>
                                                        <input type="file" class="form-control input-sm"
                                                            id="uploadFilePurchase0" name="uploadFilePurchase0[]"
                                                            style="margin-top:5px;">
                                                    </div>
                                                </div>
                                                <!-- Add/Remove buttons -->
                                                <div class="col-md-1 col-xs-2">
                                                    <button type="button" class="btn btn-primary btn-xs btnAddRow"
                                                        style="margin-top: 25px;">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs btnRemoveRow"
                                                        style="margin-top: 25px; display:none;">
                                                        <i class="glyphicon glyphicon-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding: 5px;" class="modal-footer">
                                    <button id="btnSavePurchaseDetail" class="btn btn-primary btn-sm" name="save_detail"
                                        title="Save">
                                        <i class="fa fa-check-square-o"></i> Save
                                    </button>
                                    <button id="btnCancelDetail" class="btn btn-danger btn-sm" name="btnCancel"
                                        title="Cancel" data-dismiss="modal">
                                        <i class="fa fa-ban"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt" id="idData1">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                    <thead>
                                        <tr style="background-color: #A70000;color: #FFF;height:40px;">
                                            <th style="vertical-align: middle; width:3%;text-align:center;">No</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Code / Part
                                                No</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Name of
                                                Article</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Unit</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Working</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Stock</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Request</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Mark
                                                Reference</th>
                                            <th style="vertical-align: middle; width:7%;text-align:center;">Total
                                                Approve</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Remark</th>
                                            <th style="vertical-align: middle; width:20%;text-align:center;">File Upload
                                            </th>
                                            <th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idTbodyDetail">

                                    </tbody>
                                </table>
                                <div class="col-md-12 col-xs-12" style="font-size:10px;">
                                    NOTE: <br>
                                    - A = TO BE REPLACE URGENTLY<br>
                                    - B = BETTER TO BE REPLACE<br>
                                    - C = STOCK FOR NEXT O/HAUL<br>
                                    - D = STOCK FOR EMERGENCY
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group" align="center">
                                <input type="hidden" name="txtIdReq" id="txtIdReq" value="">
                                <button id="btnSaveDetail" class="btn btn-primary btn-sm" name="btnSave" title="Save">
                                    <i class="fa fa-check-square-o"></i> Save
                                </button>
                                <button id="btnCancelDetail" onclick="reloadPage();" class="btn btn-danger btn-sm"
                                    name="btnCancel" title="Cancel">
                                    <i class="fa fa-ban"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <div class="modal fade" id="modalReqDetail" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 10px;background-color:#A70000;">
                    <button type="button" class="close" data-dismiss="modal"
                        style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
                    <h4 class="modal-title">Data Request</h4>
                </div>
                <div class="modal-body" id="idModalDetail">
                    <div class="row">
                        <div class="col-md-12">
                            <legend style="text-align: right;">
                                <span style="display:none;" id="idLoadingModal"><img
                                        src="<?php echo base_url('assets/img/loading.gif'); ?>"></span>
                                <label id="lblModal"></label>
                            </legend>
                            <div class="table-responsive" id="idTableModal">
                                <table
                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                    <thead>
                                        <tr style="background-color: #000;color: #FFF;height:40px;">
                                            <th style="vertical-align: middle; width:3%;text-align:center;">No</th>
                                            <th style="vertical-align: middle; width:25%;text-align:center;">Name of
                                                Article</th>
                                            <th style="vertical-align: middle; width:10%;text-align:center;">Code / Part
                                                No</th>
                                            <th style="vertical-align: middle; width:5%;text-align:center;">Unit</th>
                                            <th style="vertical-align: middle; width:8%;text-align:center;">Working on
                                                Board</th>
                                            <th style="vertical-align: middle; width:8%;text-align:center;">Stock on
                                                Board</th>
                                            <th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
                                            <th style="vertical-align: middle; width:8%;text-align:center;">Approved
                                                Order</th>
                                            <th style="vertical-align: middle; width:8%;text-align:center;">Mark
                                                Reference</th>
                                            <th style="vertical-align: middle; width:20%;text-align:center;">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idTbodyDetailReq">

                                    </tbody>
                                </table>
                                <div class="col-md-9" style="font-size:10px;">
                                    NOTE : <br>
                                    - A = TO BE REPLACE URGENTLY<br>
                                    - B = BETTER TO BE REPLACE<br>
                                    - C = STOCK FOR NEXT O/HAUL<br>
                                    - D = STOCK FOR EMERGENCY
                                </div>
                                <div class="col-md-2" style="font-size:10px;">
                                    <label style="color:red;font-size:16px;font-weight:bold;"
                                        id="lblModalStatus"></label>
                                </div>
                                <div class="col-md-1 col-md-12">
                                    <button type="button" id="btnExportPdfView" class="btn btn-primary btn-xs"
                                        title="Download">Download</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>