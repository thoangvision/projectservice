<?php
return array(
    '_root_'  => 'main/index/index',  // The default route
    'api/groups/(:any)' => 'api/groups/$1',
    'api/auths/(:any)' => 'api/auths/$1',
    '(:any)'  => 'main/index',
);
