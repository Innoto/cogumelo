!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var n;"undefined"!=typeof window?n=window:"undefined"!=typeof global?n=global:"undefined"!=typeof self&&(n=self),n.rectanglesIntersect=e()}}(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
// line-segments-intersect.js 
// intersection point https://en.wikipedia.org/wiki/Line%E2%80%93line_intersection
// line 1: x1,y1,x2,y2
// line 2: x3,y3,x4,y4
// for comparing the float number, fixing the number to int to required 
// precision
function linesIntersect(x1,y1,x2,y2,x3,y3,x4,y4,precision) {
  var intPt,x,y,result = false, 
    p = precision || 6,
    denominator = (x1 - x2)*(y3 - y4) - (y1 -y2)*(x3 - x4);
  if (denominator == 0) {
    // check both segments are Coincident, we already know 
    // that these two are parallel 
    if (fix((y3 - y1)*(x2 - x1),p) == fix((y2 -y1)*(x3 - x1),p)) {
      // second segment any end point lies on first segment
      result = intPtOnSegment(x3,y3,x1,y1,x2,y2,p) ||
        intPtOnSegment(x4,y4,x1,y1,x2,y2,p);
    }
  } else {
    x = ((x1*y2 - y1*x2)*(x3 - x4) - (x1 - x2)*(x3*y4 - y3*x4))/denominator;
    y = ((x1*y2 - y1*x2)*(y3 - y4) - (y1 - y2)*(x3*y4 - y3*x4))/denominator;
    // check int point (x,y) lies on both segment 
    result = intPtOnSegment(x,y,x1,y1,x2,y2,p) 
      && intPtOnSegment(x,y,x3,y3,x4,y4,p);
  }
  return result;
} 

function intPtOnSegment(x,y,x1,y1,x2,y2,p) {
  return fix(Math.min(x1,x2),p) <= fix(x,p) && fix(x,p) <= fix(Math.max(x1,x2),p) 
    && fix(Math.min(y1,y2),p) <= fix(y,p) && fix(y,p) <= fix(Math.max(y1,y2),p); 
}

// fix to the precision
function fix(n,p) {
  return parseInt(n * Math.pow(10,p));
}

module.exports = linesIntersect;

},{}]},{},[1])(1)
});


//# sourceMappingURL=line-segments-intersect.js.map