<?php
// Add this to your controller or route to debug slow queries
use Illuminate\Support\Facades\DB;

// Enable query logging
DB::enableQueryLog();

// Your query here
// $results = YourModel::with('relation')->get();

// Get executed queries
$queries = DB::getQueryLog();
foreach ($queries as $query) {
    echo "Query: " . $query['query'] . "\n";
    echo "Time: " . $query['time'] . "ms\n";
    echo "Bindings: " . json_encode($query['bindings']) . "\n\n";
}