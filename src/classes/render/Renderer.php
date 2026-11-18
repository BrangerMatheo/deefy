<?php

namespace iutnc\deefy\render;
interface Renderer {

    const compact = 1;
    const long = 2;

    public function render(int $selector) : string;

}

?>