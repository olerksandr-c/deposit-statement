<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LdapController extends Controller
{
    public function index()
    {
        return view('ldap.index');
    }
}
