var jimApp = angular.module("mainApp",  []);

jimApp.controller('mainCtrl', function($scope){
  $scope.Types  = [
  {
    name: "Chicken",
    value: 0
  }, 
  {
    name: "Meat",
    value: 1,
  }, 
  {
    name: "Fish",
    value: 2
  }];
  
  $scope.subItems = {
    0:[{ name: "Drumstick", value:0}, { name:"Thigh", value:1 }, { name:"Wing ", value:2 }],
    1:[{ name: "Lamb", value:0}, { name:"Goat ", value:1 }]
  };
});