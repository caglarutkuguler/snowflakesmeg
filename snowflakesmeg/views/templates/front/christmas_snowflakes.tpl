{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* customizable snowflake styling */
.snowflake {
  font-size: {$sizesnowflakes}em;
}

@-webkit-keyframes snowflakes-fall {
    0% {
        top: -10%
    }
    100% {
        top: 100%
    }
}

@-webkit-keyframes snowflakes-shake {
    0%, 100% {
        -webkit-transform: translateX(0);
        transform: translateX(0)
    }
    50% {
        -webkit-transform: translateX(80px);
        transform: translateX(80px)
    }
}

@keyframes snowflakes-fall {
    0% {
        top: -10%
    }
    100% {
        top: 100%
    }
}

@keyframes snowflakes-shake {
    0%, 100% {
        transform: translateX(0)
    }
    50% {
        transform: translateX(80px)
    }
}

.snowflake {
    position: fixed;
    top: -10%;
    z-index: 9999;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    cursor: default;
    -webkit-animation-name: snowflakes-fall, snowflakes-shake;
    -webkit-animation-duration: 10s, 3s;
    -webkit-animation-timing-function: linear, ease-in-out;
    -webkit-animation-iteration-count: infinite, infinite;
    -webkit-animation-play-state: running, running;
    animation-name: snowflakes-fall, snowflakes-shake;
    animation-duration: 10s, 3s;
    animation-timing-function: linear, ease-in-out;
    animation-iteration-count: infinite, infinite;
    animation-play-state: running, running
}

.fa-bell-o {
  color: #ed9b40 !important;
}

.fa-music {
  color: #cc2037 !important;
}

.fa-snowflake-o {
  color: #fff;
  text-shadow: 0 0 5px #000;
}

.snowflake:nth-of-type(0) {
    left: 1%;
    -webkit-animation-delay: 0s, 0s;
    animation-delay: 0s, 0s
}

.snowflake:nth-of-type(1) {
    left: 10%;
    -webkit-animation-delay: 1s, 1s;
    animation-delay: 1s, 1s
}

.snowflake:nth-of-type(2) {
    left: 20%;
    -webkit-animation-delay: 6s, .5s;
    animation-delay: 6s, .5s
}

.snowflake:nth-of-type(3) {
    left: 30%;
    -webkit-animation-delay: 4s, 2s;
    animation-delay: 4s, 2s
}

.snowflake:nth-of-type(4) {
    left: 40%;
    -webkit-animation-delay: 2s, 2s;
    animation-delay: 2s, 2s
}

.snowflake:nth-of-type(5) {
    left: 50%;
    -webkit-animation-delay: 8s, 3s;
    animation-delay: 8s, 3s
}

.snowflake:nth-of-type(6) {
    left: 60%;
    -webkit-animation-delay: 6s, 2s;
    animation-delay: 6s, 2s
}

.snowflake:nth-of-type(7) {
    left: 70%;
    -webkit-animation-delay: 2.5s, 1s;
    animation-delay: 2.5s, 1s
}

.snowflake:nth-of-type(8) {
    left: 80%;
    -webkit-animation-delay: 1s, 0s;
    animation-delay: 1s, 0s
}

.snowflake:nth-of-type(9) {
    left: 90%;
    -webkit-animation-delay: 3s, 1.5s;
    animation-delay: 3s, 1.5s
}

.snowflake:nth-of-type(10) {
    left: 25%;
    -webkit-animation-delay: 2s, 0s;
    animation-delay: 2s, 0s
}

.snowflake:nth-of-type(11) {
    left: 65%;
    -webkit-animation-delay: 4s, 2.5s;
    animation-delay: 4s, 2.5s
}
</style>
<div class="snowflakes" aria-hidden="true">
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-bell-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-music" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
  <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-bell-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-music" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
  </div>
  <div class="snowflake">
    <i class="fa fa-bell-o" aria-hidden="true"></i>
  </div>
</div>