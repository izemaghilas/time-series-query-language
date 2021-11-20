<?php

namespace App;

// list of SQL used keywords
enum SQLKeywords: string {
    
    case SELECT = 'SELECT';
    case FROM = 'FROM';
    case WHERE = 'WHERE';
}