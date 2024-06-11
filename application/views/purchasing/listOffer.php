<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <script src="<?php echo base_url();?>assets/js/bootstrap-select.js"></script>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
    <script type="text/javascript">
    $(document).ready(function() {
        $('[id^=slcVendorCompany]').selectpicker();
        $("input[id^='txtReqDate']").datepicker({
            dateFormat: 'yy-mm-dd',
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            defaultDate: new Date(),
        });
        $("#btnSaveDetail").click(function() {
            $("html, body").animate({
                scrollTop: 0
            }, "fast");
            var formData = new FormData();

            var fileUploadPO1 = "";
            var cekUploadPO1 = $("#filePO1").val();
            var fileUploadPO2 = "";
            var cekUploadPO2 = $("#filePO2").val();
            var fileUploadPO3 = "";
            var cekUploadPO3 = $("#filePO3").val();

            if (!cekUploadPO1) {
                cekUploadPO1 = "";
            }
            if (!cekUploadPO2) {
                cekUploadPO2 = "";
            }
            if (!cekUploadPO3) {
                cekUploadPO3 = "";
            }

            if (cekUploadPO1 == "" && cekUploadPO2 == "" && cekUploadPO3 == "") {

            } else {
                fileUploadPO1 = $("#filePO1").prop('files')[0];
                fileUploadPO2 = $("#filePO2").prop('files')[0];
                fileUploadPO3 = $("#filePO3").prop('files')[0];
            }

            var idEditDetail1 = "";
            var valIdEditDetail1 = $("input[name^='txtIdDetailReq1']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valIdEditDetail1.length; l++) {
                if (idEditDetail1 == "") {
                    idEditDetail1 = valIdEditDetail1[l];
                } else {
                    idEditDetail1 += "*" + valIdEditDetail1[l];
                }
            }

            var idEditDetail2 = "";
            var valIdEditDetail2 = $("input[name^='txtIdDetailReq2']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valIdEditDetail2.length; l++) {
                if (idEditDetail2 == "") {
                    idEditDetail2 = valIdEditDetail2[l];
                } else {
                    idEditDetail2 += "*" + valIdEditDetail2[l];
                }
            }

            var idEditDetail3 = "";
            var valIdEditDetail3 = $("input[name^='txtIdDetailReq3']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valIdEditDetail3.length; l++) {
                if (idEditDetail3 == "") {
                    idEditDetail3 = valIdEditDetail3[l];
                } else {
                    idEditDetail3 += "*" + valIdEditDetail3[l];
                }
            }

            var qty1 = "";
            var valQty1 = $("input[name^='txtQty1']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valQty1.length; l++) {
                if (qty1 == "") {
                    qty1 = valQty1[l];
                } else {
                    qty1 += "*" + valQty1[l];
                }
            }

            var qty2 = "";
            var valQty2 = $("input[name^='txtQty2']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valQty2.length; l++) {
                if (qty2 == "") {
                    qty2 = valQty2[l];
                } else {
                    qty2 += "*" + valQty2[l];
                }
            }

            var qty3 = "";
            var valQty3 = $("input[name^='txtQty3']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valQty3.length; l++) {
                if (qty3 == "") {
                    qty3 = valQty3[l];
                } else {
                    qty3 += "*" + valQty3[l];
                }
            }

            var curr1 = "";
            var valCurr1 = $("select[name^='slcCurr1']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valCurr1.length; l++) {
                if (curr1 == "") {
                    curr1 = valCurr1[l];
                } else {
                    curr1 += "*" + valCurr1[l];
                }
            }

            var curr2 = "";
            var valCurr2 = $("select[name^='slcCurr2']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valCurr2.length; l++) {
                if (curr2 == "") {
                    curr2 = valCurr2[l];
                } else {
                    curr2 += "*" + valCurr2[l];
                }
            }

            var curr3 = "";
            var valCurr3 = $("select[name^='slcCurr3']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valCurr3.length; l++) {
                if (curr3 == "") {
                    curr3 = valCurr3[l];
                } else {
                    curr3 += "*" + valCurr3[l];
                }
            }

            var price1 = "";
            var valPrice1 = $("input[name^='txtPrice1']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valPrice1.length; l++) {
                if (price1 == "") {
                    price1 = valPrice1[l];
                } else {
                    price1 += "*" + valPrice1[l];
                }
            }

            var price2 = "";
            var valPrice2 = $("input[name^='txtPrice2']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valPrice2.length; l++) {
                if (price2 == "") {
                    price2 = valPrice2[l];
                } else {
                    price2 += "*" + valPrice2[l];
                }
            }

            var price3 = "";
            var valPrice3 = $("input[name^='txtPrice3']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valPrice3.length; l++) {
                if (price3 == "") {
                    price3 = valPrice3[l];
                } else {
                    price3 += "*" + valPrice3[l];
                }
            }

            var amount1 = "";
            var valAmount1 = $("input[name^='txtTotal1']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valAmount1.length; l++) {
                if (amount1 == "") {
                    amount1 = valAmount1[l];
                } else {
                    amount1 += "*" + valAmount1[l];
                }
            }

            var amount2 = "";
            var valAmount2 = $("input[name^='txtTotal2']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valAmount2.length; l++) {
                if (amount2 == "") {
                    amount2 = valAmount2[l];
                } else {
                    amount2 += "*" + valAmount2[l];
                }
            }

            var amount3 = "";
            var valAmount3 = $("input[name^='txtTotal3']").map(function() {
                return $(this).val();
            }).get();
            for (var l = 0; l < valAmount3.length; l++) {
                if (amount3 == "") {
                    amount3 = valAmount3[l];
                } else {
                    amount3 += "*" + valAmount3[l];
                }
            }

            if ($("#txtPicVendorName1").val() == "") {
                alert("PIC Vendor Name Quotation 1 Empty..!!");
                return false;
            }

            // if($("#slcVendorCompany1").val() == "")
            // {
            // 	alert("Vendor Company Quotation 1 Empty..!!");
            // 	return false;
            // }

            if ($("#txtPicVendorName2").val() == "" && $("#txtPicVendorName3").val() == "") {
                if ($("#txtRemark").val() == "") {
                    alert("vendor 2 dan 3 kosong..!!");
                    $("#txtRemark").focus();
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    }, "fast");
                    return false;
                }
            }

            formData.append('txtIdQuot1', $("#txtIdQuot1").val());
            formData.append('txtIdQuot2', $("#txtIdQuot2").val());
            formData.append('txtIdQuot3', $("#txtIdQuot3").val());
            formData.append('idReq', $("#txtIdReq").val());
            formData.append('reqDate1', $("#txtReqDate1").val());
            formData.append('reqDate2', $("#txtReqDate2").val());
            formData.append('reqDate3', $("#txtReqDate3").val());
            formData.append('appNo1', $("#txtAppNo1").val());
            formData.append('appNo2', $("#txtAppNo2").val());
            formData.append('appNo3', $("#txtAppNo3").val());
            formData.append('picVendor1', $("#txtPicVendorName1").val());
            formData.append('picVendor2', $("#txtPicVendorName2").val());
            formData.append('picVendor3', $("#txtPicVendorName3").val());
            formData.append('vendorCompany1', $("#slcVendorCompany1 option:selected").text());
            formData.append('vendorCompany2', $("#slcVendorCompany2 option:selected").text());
            formData.append('vendorCompany3', $("#slcVendorCompany3 option:selected").text());
            formData.append('vesselName1', $("#slcShip1").val());
            formData.append('vesselName2', $("#slcShip2").val());
            formData.append('vesselName3', $("#slcShip3").val());
            formData.append('vesselCompany1', $("#slcCompany1").val());
            formData.append('vesselCompany2', $("#slcCompany2").val());
            formData.append('vesselCompany3', $("#slcCompany3").val());
            formData.append('fileUploadPO1', fileUploadPO1);
            formData.append('cekUploadPO1', cekUploadPO1);
            formData.append('fileUploadPO2', fileUploadPO2);
            formData.append('cekUploadPO2', cekUploadPO2);
            formData.append('fileUploadPO3', fileUploadPO3);
            formData.append('cekUploadPO3', cekUploadPO3);
            formData.append('idEditDetail1', idEditDetail1);
            formData.append('idEditDetail2', idEditDetail2);
            formData.append('idEditDetail3', idEditDetail3);
            formData.append('qty1', qty1);
            formData.append('qty2', qty2);
            formData.append('qty3', qty3);
            formData.append('curr1', curr1);
            formData.append('curr2', curr2);
            formData.append('curr3', curr3);
            formData.append('price1', price1);
            formData.append('price2', price2);
            formData.append('price3', price3);
            formData.append('amount1', amount1);
            formData.append('amount2', amount2);
            formData.append('amount3', amount3);
            formData.append('txtRemark', $("#txtRemark").val());
            formData.append('txtDiscQuot1', $("#txtDiscountQuot1").val());
            formData.append('txtDiscQuot2', $("#txtDiscountQuot2").val());
            formData.append('txtDiscQuot3', $("#txtDiscountQuot3").val());
            formData.append('txtPPNQuot1', $("#txtPPNQuot1").val());
            formData.append('txtPPNQuot2', $("#txtPPNQuot2").val());
            formData.append('txtPPNQuot3', $("#txtPPNQuot3").val());
            formData.append('txtOngkirQuot1', $("#txtDeliveryQuot1").val());
            formData.append('txtOngkirQuot2', $("#txtDeliveryQuot2").val());
            formData.append('txtOngkirQuot3', $("#txtDeliveryQuot3").val());
            formData.append('txtKursQuot1', $("#txtKursQuot1").val());
            formData.append('txtKursQuot2', $("#txtKursQuot2").val());
            formData.append('txtKursQuot3', $("#txtKursQuot3").val());

            $("#idLoading").show();
            $(this).attr("disabled", "disabled");

            $.ajax("<?php echo base_url('offered/addOffered'); ?>", {
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
    });

    $(document).ready(function() {
        $("#btnSearchOffer").click(function() {
            var idSlcType = $("#idSlcType").val();
            var valSearch = $("#txtSearch").val();

            if (valSearch === "") {
                alert("Search Empty..!!");
                return false;
            }

            $("#idLoading").show();

            $.post('<?php echo base_url("offered/getListOffer"); ?>/search/', {
                valSearch: valSearch,
                idSlcType: idSlcType
            }, function(data) {
                // Mengosongkan tbody
                $("#idTbody").empty();
                // Mengosongkan halaman (jika ada elemen untuk pagination)
                $("#idPage").empty();

                // Pastikan data yang diterima adalah dalam format JSON
                if (data.trNya) {
                    $("#idTbody").append(data.trNya);
                }

                $("#idLoading").hide();
            }, "json").fail(function(xhr, status, error) {
                console.error("Error: " + error);
                $("#idLoading").hide();
            });
        });
    });

    //penambahan code
    function completeTask(id) {
        console.log("completeTask called with id: " + id); // Debugging
        var idReq = id;
        $("#idTableModal").empty();
        $("#lblModal").text("Complete Remark");
        var divNya = "";
        divNya += "<div class='row'>";
        divNya += "<div class='col-md-12'>";
        divNya += "<div class='col-md-2'>";
        divNya += "<label>Remark :</label>";
        divNya += "</div>";
        divNya += "<div class='col-md-10'>";
        divNya += "<textarea class='form-control input-sm' id='txtCompleteModal'></textarea>";
        divNya += "</div>";
        divNya += "</div>";
        divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
        divNya += "<button id='btnSubmitModalReq' onclick='submitComplete(" + idReq +
            ");' class='btn btn-primary btn-sm' title='Complete'><i class='fa fa-check-square-o'></i> Submit</button> ";
        divNya +=
            "<button id='btnCancelModalReq' class='btn btn-danger btn-sm' data-dismiss='modal' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
        divNya += "</div>";
        divNya += "</div>";
        $("#idTableModal").append(divNya);
        $('#modalOfferDetail').modal('show');
    }

    function submitComplete(id) {
        console.log("submitComplete called with id: " + id); // Debugging
        var idReq = id;
        var remark = $("#txtCompleteModal").val();

        if (remark == "") {
            alert("Remark can't be blank..!!");
            return false;
        }

        var cfm = confirm("Submit Complete Remark..??");
        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("offered/submitComplete"); ?>/', {
                    idReq: idReq,
                    remark: remark
                },
                function(response) {
                    $("#idLoading").hide();
                    console.log(response);

                    if (typeof response === "string") {
                        response = JSON.parse(response);
                    }
                    if (response.success) {
                        alert(response.message);
                        $("#modalOfferDetail").modal('hide'); // Hide the modal on success
                        $("#row-" + idReq).remove(); // Remove the row from the table
                        $("#row-" + idReq + " .remark").text(remark); // Update remark in table
                    } else {
                        alert("Failed to submit: " + response.message);
                    }
                },
                "json"
            ).fail(function(jqXHR, textStatus, errorThrown) {
                $("#idLoading").hide();
                alert("Error: " + textStatus + ", " + errorThrown);
            });
        }
    }


    function createQuotation(idReq) {
        $("#idLoading").show();
        $.post('<?php echo base_url("offered/getEdit"); ?>/', {
                id: idReq,
                typeEdit: "createQuotation"
            },
            function(data) {
                $("#idDataTable").hide();
                $.each(data.dataNya, function(i, item) {
                    $("input[id^='txtReqDate']").val(item.date_request);
                    $("input[id^='txtAppNo']").val(item.app_no);
                    $("select[id^='slcShip']").val(item.vessel);
                });
                $("#txtIdReq").val(data.idReq);
                $("#idTbodyQuot1").empty();
                $("#idTbodyQuot1").append(data.trNya1);
                $("#idTbodyQuot2").empty();
                $("#idTbodyQuot2").append(data.trNya2);
                $("#idTbodyQuot3").empty();
                $("#idTbodyQuot3").append(data.trNya3);
                $("#idLoading").hide();
                $("#idFormDetail").show();
            },
            "json"
        );
    }

    function submitData(idReq) {
        var cfm = confirm("Submit Data..??");

        if (cfm) {
            $("#idLoading").show();
            $.post('<?php echo base_url("offered/submitData"); ?>/', {
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

    function editData(idReq) {
        $("#idLoading").show();
        $.post('<?php echo base_url("offered/getEdit"); ?>/', {
                id: idReq,
                typeEdit: "editQuotation"
            },
            function(data) {
                $("#idDataTable").hide();
                $("#txtIdReq").val(data.idReq);
                $("#txtIdQuot1").val(data.dataQuot.idQuot1);
                $("#txtIdQuot2").val(data.dataQuot.idQuot2);
                $("#txtIdQuot3").val(data.dataQuot.idQuot3);

                for (var ast = 1; ast <= 3; ast++) {
                    $("#slcVendorCompany" + ast).empty();
                    $("#slcVendorCompany" + ast).append(data.optSupplier);
                    $("#slcVendorCompany" + ast).selectpicker('refresh');
                }

                $("#txtReqDate1").val(data.dataQuot.reqDate1);
                $("#txtReqDate2").val(data.dataQuot.reqDate2);
                $("#txtReqDate3").val(data.dataQuot.reqDate3);

                $("#txtAppNo1").val(data.dataQuot.appNo1);
                $("#txtAppNo2").val(data.dataQuot.appNo2);
                $("#txtAppNo3").val(data.dataQuot.appNo3);

                $("#txtPicVendorName1").val(data.dataQuot.picVendor1);
                $("#txtPicVendorName2").val(data.dataQuot.picVendor2);
                $("#txtPicVendorName3").val(data.dataQuot.picVendor3);

                $("#slcVendorCompany1").val(data.dataQuot.vendorCode1);
                $("#slcVendorCompany2").val(data.dataQuot.vendorCode2);
                $("#slcVendorCompany3").val(data.dataQuot.vendorCode3);

                $("#slcShip1").val(data.dataQuot.vesselName1);
                $("#slcShip2").val(data.dataQuot.vesselName2);
                $("#slcShip3").val(data.dataQuot.vesselName3);

                $("#slcVendorCompany1").selectpicker('refresh');
                $("#slcVendorCompany2").selectpicker('refresh');
                $("#slcVendorCompany3").selectpicker('refresh');

                $("#slcCompany1").val(data.dataQuot.vesselCompany1);
                $("#slcCompany2").val(data.dataQuot.vesselCompany2);
                $("#slcCompany3").val(data.dataQuot.vesselCompany3);

                $("#linkPO1").append(data.dataQuot.linkFile1);
                $("#linkPO2").append(data.dataQuot.linkFile2);
                $("#linkPO3").append(data.dataQuot.linkFile3);

                $("#idTbodyQuot1").empty();
                $("#idTbodyQuot1").append(data.trNya1);
                $("#idTbodyQuot2").empty();
                $("#idTbodyQuot2").append(data.trNya2);
                $("#idTbodyQuot3").empty();
                $("#idTbodyQuot3").append(data.trNya3);

                $("#txtRemark").val(data.remarkOffered);

                $("#idLoading").hide();
                $("#idFormDetail").show();
            },
            "json"
        );
    }

    function getSupplierErp(companyNya, quotKe) {
        if (companyNya == "") {
            alert("Company Empty..!!");
            $("#slcVendorCompany" + quotKe).empty();
            $("#slcVendorCompany" + quotKe).append('<option value="">- Select -</option>');
        } else {
            $("#idLoading").show();
            $.post('<?php echo base_url("offered/getSupplierErp"); ?>/', {
                    companyNya: companyNya
                },
                function(data) {
                    $("#slcVendorCompany" + quotKe).empty();
                    //$("#slcVendorCompany"+quotKe).append('<option value="">- Select -</option>');
                    $("#slcVendorCompany" + quotKe).append(data);
                    $("#slcVendorCompany" + quotKe).selectpicker('refresh');

                    $("#idLoading").hide();
                },
                "json"
            );
        }
    }

    function sumData(id, idTotal) {
        var qty = $("#txtQty" + idTotal + "_" + id).val();
        var price = $("#txtPrice" + idTotal + "_" + id).val();
        var total = 0;
        var subTotal = 0;
        var disc = $("#txtDiscountQuot" + idTotal).val();
        var afterDisc = 0;
        var ppn = $("#txtPPNQuot" + idTotal).val();
        var ongkir = $("#txtDeliveryQuot" + idTotal).val();
        var grandTotal = 0;

        if (qty == "") {
            $("#txtQty" + idTotal + "_" + id).val("0");
            qty = "0";
        }

        if (price == "") {
            $("#txtPrice" + idTotal + "_" + id).val("0");
            price = "0";
        }

        if (disc == "") {
            disc = 0;
        }

        if (ppn == "") {
            ppn = 0;
        }

        if (ongkir == "") {
            ongkir = 0;
        }

        total = parseFloat(qty) * parseFloat(price);
        $("#txtTotal" + idTotal + "_" + id).val(total);

        var valAmount = $("input[name^='txtTotal" + idTotal + "']").map(function() {
            return $(this).val();
        }).get();
        for (var l = 0; l < valAmount.length; l++) {
            subTotal = parseFloat(subTotal) + parseFloat(valAmount[l]);
        }
        cekTotalApprove(id, idTotal);
        $("#idTotalQuot" + idTotal).text(subTotal.toLocaleString(2));

        afterDisc = parseFloat(subTotal) - parseFloat(disc);
        $("#idAfterDiscQuot" + idTotal).text(afterDisc.toLocaleString(2));

        grandTotal = parseFloat(afterDisc) + parseFloat(ppn) + parseFloat(ongkir);
        $("#idGrandTotalQuot" + idTotal).text(grandTotal.toLocaleString(2));

        sumDataByDiscPPnOngkir(idTotal);
    }

    function sumDataByDiscPPnOngkir(idTotal) {
        var total = 0;
        var subTotal = 0;
        var disc = $("#txtDiscountQuot" + idTotal).val();
        var afterDisc = 0;
        var ppn = $("#txtPPNQuot" + idTotal).val();
        var ongkir = $("#txtDeliveryQuot" + idTotal).val();
        var grandTotal = 0;

        if (disc == "") {
            disc = 0;
        }

        if (ppn == "") {
            ppn = 0;
        }

        if (ongkir == "") {
            ongkir = 0;
        }

        var valAmount = $("input[name^='txtTotal" + idTotal + "']").map(function() {
            return $(this).val();
        }).get();
        for (var l = 0; l < valAmount.length; l++) {
            subTotal = parseFloat(subTotal) + parseFloat(valAmount[l]);
        }

        afterDisc = parseFloat(subTotal) - parseFloat(disc);
        $("#idAfterDiscQuot" + idTotal).text(afterDisc.toLocaleString(2));

        grandTotal = parseFloat(afterDisc) + parseFloat(ppn) + parseFloat(ongkir);
        $("#idGrandTotalQuot" + idTotal).text(grandTotal.toLocaleString(2));

        konversiIdr(idTotal);
    }

    function konversiIdr(idQuot) {
        var aftKurs = 0;
        var grandTotal = $("#idGrandTotalQuot" + idQuot).text();
        var kurs = $("#txtKursQuot" + idQuot).val();

        if (kurs != "" && parseFloat(kurs) > 0) {
            grandTotal = grandTotal.replace(/,/g, "");
            aftKurs = parseFloat(grandTotal) * parseFloat(kurs);

            $("#idTotalAfterKursQuot" + idQuot).text(aftKurs.toLocaleString(2));
        } else {
            $("#idTotalAfterKursQuot" + idQuot).text(grandTotal.toLocaleString(2));
        }
    }

    function cekTotalApprove(id, idDiv) {
        var qty = $("#txtQty" + idDiv + "_" + id).val();
        var lblApprove = $("#idLblAppOrder" + idDiv + "_" + id).text();

        if (parseFloat(qty) > parseFloat(lblApprove)) {
            $("#txtQty" + idDiv + "_" + id).val(lblApprove);
        }
    }

    function reloadPage() {
        window.location = "<?php echo base_url('offered/getListOffer');?>";
    }
    </script>
</head>

<body>
    <section id="container">
        <section id="main-content">
            <section class="wrapper site-min-height" style="min-height:400px;">
                <h3>
                    <i class="fa fa-angle-right"></i> Entry Quotation<span style="display:none;padding-left:20px;"
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
                            <button type="button" id="btnSearchOffer" class="btn btn-warning btn-sm btn-block"
                                title="Add"><i class="fa fa-search"></i> Search</button>
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
                                            <th style="vertical-align: middle; width:5%;text-align:center;">No</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Req. Date
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">App. No
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Vessel</th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Department
                                            </th>
                                            <th style="vertical-align: middle; width:15%;text-align:center;">Status</th>
                                            <th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="idTbody">
                                        <?php echo $trNya; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modalOfferDetail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="padding: 10px; background-color: #A70000;">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><label id="lblModal"></label></h4>
                            </div>
                            <div class="modal-body">
                                <div id="idTableModal"></div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="form-panel" id="idFormDetail" style="display:none;">
                    <div class="panel-group" id="idQuotation">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#idQuotation" href="#idQuot1">
                                        Quotation 1
                                    </a>
                                </h4>
                            </div>
                            <div id="idQuot1" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <legend style="text-align:right;">Quotation 1</legend>
                                    <div class="row">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtReqDate1">Request Date :</label>
                                            <input type="text" id="txtReqDate1" class="form-control input-sm"
                                                value="<?php echo date("Y-m-d"); ?>">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtAppNo1">App No :</label>
                                            <input type="text" id="txtAppNo1" placeholder="Request No"
                                                class="form-control input-sm">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="filePO1">File Quotation 1 :</label> <label id="linkPO1"
                                                style="float:right;"></label>
                                            <input type="file" id="filePO1" name="filePO1"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row" style="padding: 10px 0px 10px 0px;">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcShip1">Ship Name :</label>
                                            <select name="slcShip1" id="slcShip1" class="form-control input-sm"
                                                disabled="disabled">
                                                <option value="-">- Select -</option>
                                                <?php echo $vslNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcCompany1">Ship Company :</label>
                                            <select name="slcCompany1" id="slcCompany1" class="form-control input-sm"
                                                onchange="getSupplierErp($(this).val(),'1')">
                                                <option value="">- Select -</option>
                                                <?php echo $companyNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcVendorCompany1">Vendor Company :</label>
                                            <select name="slcVendorCompany1" id="slcVendorCompany1"
                                                class="form-control input-sm" data-live-search="true">
                                                <!-- <option value="">- Select -</option> -->
                                            </select>
                                            <!-- <input type="text" id="txtVendorCompany1" placeholder="Company" class="form-control input-sm"> -->
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtPicVendorName1">PIC Vendor Name :</label>
                                            <input type="text" id="txtPicVendorName1" placeholder="Name"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row mt" id="idDataQuot1">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                                    <thead>
                                                        <tr style="background-color: #A70000;color: #FFF;height:40px;">
                                                            <th
                                                                style="vertical-align: middle; width:5%;text-align:center;">
                                                                No</th>
                                                            <th
                                                                style="vertical-align: middle; width:15%;text-align:center;">
                                                                Name of Article</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Code / Part No</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Unit</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Request</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Total Approve</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Quantity</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                            </th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Price</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="idTbodyQuot1">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#idQuotation" href="#idQuot2">
                                        Quotation 2
                                    </a>
                                </h4>
                            </div>
                            <div id="idQuot2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <legend style="text-align:right;">Quotation 2</legend>
                                    <div class="row">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtReqDate2">Request Date :</label>
                                            <input type="text" id="txtReqDate2" class="form-control input-sm"
                                                value="<?php echo date("Y-m-d"); ?>">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtAppNo2">App No :</label>
                                            <input type="text" id="txtAppNo2" placeholder="Request No"
                                                class="form-control input-sm">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="filePO2">File Quotation 2 :</label> <label id="linkPO2"
                                                style="float:right;"></label>
                                            <input type="file" id="filePO2" name="filePO2"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row" style="padding: 10px 0px 10px 0px;">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcShip2">Ship Name :</label>
                                            <select name="slcShip2" id="slcShip2" class="form-control input-sm"
                                                disabled="disabled">
                                                <option value="">- Select -</option>
                                                <?php echo $vslNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcCompany2">Ship Company :</label>
                                            <select name="slcCompany2" id="slcCompany2" class="form-control input-sm"
                                                onchange="getSupplierErp($(this).val(),'2')">
                                                <option value="">- Select -</option>
                                                <?php echo $companyNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcVendorCompany2">Vendor Company :</label>
                                            <select name="slcVendorCompany2" id="slcVendorCompany2"
                                                class="form-control input-sm" data-live-search="true">
                                                <!-- <option value="">- Select -</option> -->
                                            </select>
                                            <!-- <input type="text" id="txtVendorCompany2" placeholder="Company" class="form-control input-sm"> -->
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtPicVendorName2">PIC Vendor Name :</label>
                                            <input type="text" id="txtPicVendorName2" placeholder="Name"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row mt" id="idDataQuot2">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                                    <thead>
                                                        <tr style="background-color: #A70000;color: #FFF;height:40px;">
                                                            <th
                                                                style="vertical-align: middle; width:5%;text-align:center;">
                                                                No</th>
                                                            <th
                                                                style="vertical-align: middle; width:15%;text-align:center;">
                                                                Name of Article</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Code / Part No</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Unit</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Request</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Total Approve</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Quantity</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                            </th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Price</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="idTbodyQuot2">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#idQuotation" href="#idQuot3">
                                        Quotation 3
                                    </a>
                                </h4>
                            </div>
                            <div id="idQuot3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <legend style="text-align:right;">Quotation 3</legend>
                                    <div class="row">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtReqDate3">Request Date :</label>
                                            <input type="text" id="txtReqDate3" class="form-control input-sm"
                                                value="<?php echo date("Y-m-d"); ?>">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtAppNo3">App No :</label>
                                            <input type="text" id="txtAppNo3" placeholder="Request No"
                                                class="form-control input-sm">
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="filePO3">File Quotation 3 :</label> <label id="linkPO3"
                                                style="float:right;"></label>
                                            <input type="file" id="filePO3" name="filePO3"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row" style="padding: 10px 0px 10px 0px;">
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcShip3">Ship Name :</label>
                                            <select name="slcShip3" id="slcShip3" class="form-control input-sm"
                                                disabled="disabled">
                                                <option value="">- Select -</option>
                                                <?php echo $vslNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcCompany3">Ship Company :</label>
                                            <select name="slcCompany3" id="slcCompany3" class="form-control input-sm"
                                                onchange="getSupplierErp($(this).val(),'3')">
                                                <option value="">- Select -</option>
                                                <?php echo $companyNya; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="slcVendorCompany3">Vendor Company :</label>
                                            <select name="slcVendorCompany3" id="slcVendorCompany3"
                                                class="form-control input-sm" data-live-search="true">
                                                <!-- <option value="">- Select -</option> -->
                                            </select>
                                            <!-- <input type="text" id="txtVendorCompany3" placeholder="Company" class="form-control input-sm"> -->
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <label for="txtPicVendorName3">PIC Vendor Name :</label>
                                            <input type="text" id="txtPicVendorName3" placeholder="Name"
                                                class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="row mt" id="idDataQuot3">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
                                                    <thead>
                                                        <tr style="background-color: #A70000;color: #FFF;height:40px;">
                                                            <th
                                                                style="vertical-align: middle; width:5%;text-align:center;">
                                                                No</th>
                                                            <th
                                                                style="vertical-align: middle; width:15%;text-align:center;">
                                                                Name of Article</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Code / Part No</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Unit</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Request</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Total Approve</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                                Quantity</th>
                                                            <th
                                                                style="vertical-align: middle; width:8%;text-align:center;">
                                                            </th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Price</th>
                                                            <th
                                                                style="vertical-align: middle; width:10%;text-align:center;">
                                                                Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="idTbodyQuot3">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12 col-xs-12">
                                <label for="txtReqDate1">Remark ( <i style="color:red;font-size:10px;">* Jika Vendor
                                        hanya 1 Wajib isi Remark</i> &nbsp) :</label>
                                <textarea id="txtRemark" class="form-control input-sm"></textarea>
                            </div>
                        </div>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group" align="center">
                                    <input type="hidden" name="txtIdQuot1" id="txtIdQuot1" value="">
                                    <input type="hidden" name="txtIdQuot2" id="txtIdQuot2" value="">
                                    <input type="hidden" name="txtIdQuot3" id="txtIdQuot3" value="">
                                    <input type="hidden" name="txtIdReq" id="txtIdReq" value="">
                                    <button id="btnSaveDetail" class="btn btn-primary btn-sm" name="btnSave"
                                        title="Save">
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
                </div>
            </section>
        </section>
    </section>
</body>

</html>