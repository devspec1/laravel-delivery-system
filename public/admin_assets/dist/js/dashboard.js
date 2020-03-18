/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/

$(function () {

  "use strict";

  //Make the dashboard widgets sortable Using jquery UI
  $(".connectedSortable").sortable({
    placeholder: "sort-highlight",
    connectWith: ".connectedSortable",
    handle: ".box-header, .nav-tabs",
    forcePlaceholderSize: true,
    zIndex: 999999
  });
  $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");

  //jQuery UI sortable for the todo list
  $(".todo-list").sortable({
    placeholder: "sort-highlight",
    handle: ".handle",
    forcePlaceholderSize: true,
    zIndex: 999999
  });

  //The Calender
  $("#calendar").datepicker();

  if ($('#line-chart-data').length>=1) {
    var line = new Morris.Line({
      element: 'line-chart',
      resize: true,
      data: JSON.parse($('#line-chart-data').val()),
      xkey: 'y',
      ykeys: ['amount'],
      labels: ['Amount'],
      lineColors: ['#efefef'],
      lineWidth: 2,
      hideHover: 'auto',
      gridTextColor: "#fff",
      gridStrokeWidth: 0.4,
      pointSize: 4,
      pointStrokeColors: ["#efefef"],
      gridLineColor: "#efefef",
      gridTextFamily: "Open Sans",
      gridTextSize: 10
    });
  }
});
