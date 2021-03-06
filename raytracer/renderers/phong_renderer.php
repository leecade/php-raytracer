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

/**
 * Phong (specular) rendering involves looking at the angle between the ray and the light.
 * This makes objects look shiny.
 */

class PhongRenderer extends Renderer {
  protected function render_ray(World $world, Ray $ray, $ignore, $recursion) {
    $r = $this->rayIntersection($world, $ray, $ignore, true, true);
    if (!$r) {
      // ray does not intersect any object
      return null;
    }

    $light_ray = $this->pointLight($world, $r['p'], $r['o']);
    if (!$light_ray) {
      // object is not exposed to any lights
      return null;
    }

    // Calculate pixel's color
    $diffuse_shading = max(Vector::dot($light_ray->getDirection(), $r['n']), 0);

    $reflected_ray = Ray::reflectedRay($ray, $r['n'], $r['p']);

    $specular_shading = max(Ray::dot($light_ray, $reflected_ray), 0);
    $specular_shading = pow($specular_shading, 16);

    $reflection_shading = null;
    if ($this->reflections && ($recursion < 3)) {
      if ($r['o']->getName() == 'white sphere') {
        $reflection_shading = $this->render_ray($world, $reflected_ray, $r['o'], $recursion+1);
      }
    }
    $total = 0.7 * $diffuse_shading + 0.3 * $specular_shading;
    $c = clone ($r['o']->getColor());
    $c->K_mul(min($total, 1));

    if (($recursion == 1) && $reflection_shading) {
      $c->K_mul(0.2);
      $c2 = clone $reflection_shading;
      $c2->K_mul(0.8);
      $c->V_add($c2);
      return $c;
    } else {
      return $c;
    }
  }
}
