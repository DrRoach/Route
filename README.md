Route
===

Route is a simple and lightweight routing plugin that allows you to quickly use controllers and views without all
of the extra bulk from MVC frameworks.

It means that you can have nice looking URLs like `/news/7` instead of nasty looking ones like `/news.php?id=7` without all of the extra bulk or learning curve of mainstream MVCs.

Better suited to front end websites with very little database interaction, route is simple to set up and easy to use.

Getting Started
====

1. Clone this repo into your working directory.
2. Create your controller. The controller loaded is the first part of the URL. For example, in
 `www.yoursite.com/news/15`, the controller looked for will be `NewsController.php`.
3. Create your function. The function loaded is the second part of the URL. For example, in
 `www.yoursite.com/friends/add/barry`, the function loaded will be `add()`. However, if the second part of the
 URL is a number, like 4, the function loaded is `index()`.
4. Create your template. Each controller must have it's own folder of templates. Each named after their parent
 method in the Controller. For example, if you called 'www.yoursite.com/friends/add', the template loaded will be
 `Templates/friends/add.php`.

Using controller variables is views
====

It's easy to pass data from your controller to your view. Just return the data from your controller as an array.
 The easiest way to do this is using the `compact()` method. Say you had a variable called `$data`. To pass it to
 the view, just call `return compact('data');`.

Defaults
====

At the moment, there are only two default settings in this project. The first is that the 'HomeController' is always
 the default controller. The second is that the default method to be called is `index()`.

In the future, I am aiming to make the system fully dynamic so that there are no defaults at all or at least a setting
 file.

Future Updates
====

- [ ] Easy database integration.
- [ ] More choice over views loaded.

**Other suggestions are much appreciated!**

If you do have any ideas, feel free to open an issue.
