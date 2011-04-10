<?php
/**
 * Copyright 2011, Alok Menghrajani. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 *    conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 *    of conditions and the following disclaimer in the documentation and/or other materials
 *    provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of the author.
 */

class Plane extends Object {
  protected $normal;

  public function setNormal(Vector $n) {
    $this->normal = clone $n;
    $this->normal->normalize();
    return $this;
  }

  public function intersect(Ray $ray, $compute_point, $compute_normal) {
    $d = -$this->position->V_dot($this->normal);
    $denom = $this->normal->V_dot($ray->getDirection());
    if ($denom == 0) {
      return null;
    }
    $num = -$d - $this->normal->V_dot($ray->getOrigin());
    $t = $num / $denom;

    if ($t < 0) {
      return null;
    }

    $r = array('d' => $t);

    if ($compute_point || $compute_normal) {
      $t2 = clone $ray->getDirection();
      $t2->K_mul($t);
      $t2->V_add($ray->getOrigin());

      $r['p'] = $t2;

      $r['n'] = $this->normal;
    }
    return $r;
  }

/*
    $new_ray = new Ray();
    $new_ray->setOrigin($t2);
    foreach ($world->getLights() as $light) {
      $d = clone $new_ray->getOrigin();
      $d->neg();
      $d->V_add($light->getPosition());
      $new_ray->setDirection($d);

      foreach ($world->getObjects() as $obj) {
        if ($obj === $this) {
          continue;
        }
        $obj->intersect($new_ray, $world, 1);
        if ($new_ray->getDistance() !== null) {
          return;
        }
      }
    }

    // diffuse shading
    $shading = max($d->V_dot($this->normal) / $d->length(), 0);

    // Fog effect
//    $total_distance = $t + $d->length();
//    $intensity = min(1, 1 / $total_distance * 1000);

    $c = clone $this->color;
//    $ray->setIntersect($t, $c->K_mul($intensity * $shading));
    $ray->setIntersect($t, $c->K_mul($shading));
  }
*/

}
