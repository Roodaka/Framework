<?php

namespace Framework\Controllers;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Home Extends \Framework\Controller
 {

  public function build_header()
   {
    \Framework\View::add_key('title', 'asd');
    \Framework\View::add_file('css', 'asd.js'); // Agregamos un archivo aleatorio
   }



  public function main()
   {
    $users = \Framework\Cache::get('main_users');
    if($users === false)
     {
      $users = \Framework\Factory::create_from_database('Example', null, 'name', 10, true);
      \Framework\Cache::set('main_users', $users);
     }
    

    \Framework\View::add_key('users', $users);

    \Framework\View::add_template('home');
    //return Framework\Core::redirect('Other');
   }



  public function edit()
   {
    $id = get_routing_value();
    if($id !== null)
     {
      $user = load_model('Example', (int) $id, null, true);

      if($this->post_count >= 2)
       {
        $user->name = $this->post['name'];
        $user->lastname = $this->post['lastname'];
        return \Framework\Core::redirect('home', 'main');
       }
      else
       {
        \Framework\View::add_key('user', $user->get_array());
        \Framework\View::add_template('edit');
       }
     }
    else
     {
      return \Framework\Core::redirect('home', 'main');
     }
   }



  public function create()
   {
    if($this->post_count >= 2)
     {
      $new_user = load_model('Example');
      $new_user->name = $this->post['name'];
      $new_user->lastname = $this->post['lastname'];
      $new_user->datetime = time();

      if($new_user->save() === true)
       {
        \Framework\Core::redirect('home', 'main');
       }
     }
    else
     {
      \Framework\View::add_template('create');
     }
   }



  public function delete()
   {
    $id = get_routing_value();
    if($id !== null)
     {
      $user = load_model('Example', (int) $id, null, true);
      $user->set_to_delete();
     }
    return \Framework\Core::redirect('home', 'main');
   }



  public function clear()
   {
    \Framework\Cache::clear();
    return \Framework\Core::redirect('home', 'main');
   }
 } // class Home Extends Controller