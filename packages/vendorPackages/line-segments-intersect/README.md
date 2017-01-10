#line-segments-intersect
Determine if two line segments intersect each other. 
## installation

```
npm install line-segments-intersect
```

## usage
```
var intersects = require('line-segments-intersect');
intersects(segment1,segment2 [,precision]);
```
**segment1** array of two points  
**segment2** array of two points  
whereas point is array of x and y coordinates  
**precision** after decimal places, default is 6  

## example
```javascript
var intersects = require('line-segments-intersect');
if (intersects([[0,0],[10,10]], [[10,0],[0,10]]) {
  console.log('two segments intersect');
}
```
## reference
Intersection point for internal calculation is  calculated as given [here](https://en.wikipedia.org/wiki/Line%E2%80%93line_intersection)  
## developing
Once you run
 
```npm isntall```

then for running test 

```npm run test```

to create build

```npm run build```

## license
This project is licensed under the terms of the MIT license.
