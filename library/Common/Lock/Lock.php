<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-04 15:40
 */

namespace Common\Lock;

interface Lock {
    public function lock();
    public function unlock();
} 