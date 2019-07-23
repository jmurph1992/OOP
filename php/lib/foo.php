<?php

namespace Justin\OOP;

require_once("../Classes/Foo.php");

$foo = new Author("29f60234-ed41-4191-87d5-f81ff4583107", "hello.com", "f73718223aeefedecabc880f7772c849",
	"hello@me.com", '$argon2i$v=19$m=1024,t=384,p=2$dE55dm5kRm9DTEYxNFlFUA$nNEMItrDUtwnDhZ41nwVm7ncBLrJzjh5mGIjj8RlzVA', "hello");

var_dump($foo);