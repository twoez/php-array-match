# PHP Array Match

Search a recursive array with string patterns e.g. `*.hello.*.world` (also supports wildcard).

Example:
```
$array = [
    'foo' => [
        'bar' => 'value'
        'hello' => [
            [
                'world' => 'value'
            ]
        ]
    ],
];

// Both patterns will match the array above.
$pattern1='foo.bar';
$pattern2='*.hello.*.world';

// To perform a search you can use the following. 
// To search for "$pattern2" you would have to run another "match".
$hasMatch = ArrayMatch::match($pattern1, $array, $matches);

if ($hasMatch) {
    foreach ($matches as $match) {
        var_dump($match['track']);
        var_dump($match['value']);
        ArrayMatch::replace($match['track'], 'my new value', $array);
    }
}