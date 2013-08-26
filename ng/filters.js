'use strict';

/* Filters */

angular.module('Peon.filters', [])
.filter('shortUrl', function() {
  return function(temp) {
    return temp.replace('//', '').split(':')[1];
  }
})
.filter('mhs', function() {
  return function(hs) {
    if(hs<1000){
      return hs+" M";
    }
    hs/=1000;
    return (hs<1000)?(hs).toPrecision(4)+" G":(hs/1000).toPrecision(4)+" T";
  }
});