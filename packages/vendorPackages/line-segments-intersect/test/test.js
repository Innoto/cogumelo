var expect = require('chai').expect,
  intersects = require('../line-segments-intersect.js');
describe('check line segments interseect: ', function() {
  it('segments intersect', function() {
    expect(intersects([[0,0],[10,10]], [[10,0],[0,10]])).to.be.true;
  });
  it('segments do not intersect', function() {
    expect(intersects([[0,0],[10,10]], [[30,0],[0,30]])).to.be.false;
  });
  it('segment end touches another', function() {
    expect(intersects([[0,0],[10,10]], [[20,0],[0,20]])).to.be.true;
  });
  it('segments are parallel', function() {
    expect(intersects([[0,0],[10,10]], [[10,0],[20,10]])).to.be.false;
  });
  it('segments overlaps', function() {
    expect(intersects([[0,0],[10,10]], [[5,5],[20,20]])).to.be.true;
  });
  it('segments are coincident but do not overlap', function() {
    expect(intersects([[0,0],[10,10]], [[20,20],[30,30]])).to.be.false;
  });
  it('intersection lies only on one segment', function() {
    expect(intersects([[-50,0],[0,-50]], [[0,0],[5,0]])).to.be.false;
  });
});
