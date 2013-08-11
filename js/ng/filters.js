'use strict';

/* Filters */

angular.module('Peon.filters', [])
.filter('fromNow', function() {
	return function(date) {
		return moment(date).fromNow();
	}
})
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
})
.filter('hashps', function() {
	return function(hs) {return hs+"H/s";}
})
.filter('fromStamp', function() {
	return function(timestamp) {
		var m=moment.unix(timestamp);
		return (m.minutes()>0?m.minutes()+'m ':'')+m.seconds()+'s';
	}
});