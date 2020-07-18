# Process runner
This package provides simple services to run commands within a php process.
Each set of commands is called a `TaskList` and each TaskList contains a set of Tasks. A `TaskList` can be processed
by an `Executor` which takes the commands from each task and runs it.

### Creating a TaskList
The creation of a tasklist can be done manually like this:
```php
use Nusje2000\ParallelProcess\Task;
use Nusje2000\ParallelProcess\TaskList;
use Symfony\Component\Process\Process;

$list = new TaskList([
    new Task('task name', new Process(['some-command'])),
    new Task('task name', new Process(['some-other-command'])),
]);
```
Or using the provided factory for simple task lists:
```php
use Nusje2000\ParallelProcess\Factory\TaskListFactory;

$list = TaskListFactory::createFromArray([
    'task name' => 'command argument --option'
]);
```

### Executing a TaskList
The execution of command is done by an Executor, this package
provides two executors: ParallelExecutor and SequentialExecutor.

You can try them by running `php vendor/nusje2000/process-runner/bin/example.php`. This runs a set of commands using
both the sequential and parallel executor. 

#### Using the parallel executor
The ParallelExecutor runs all the tasks at the same time.
```php
use Nusje2000\ParallelProcess\Executor\ParallelExecutor;
use Nusje2000\ParallelProcess\TaskList;

$executor = new ParallelExecutor();
$executor->execute(new TaskList([]));
```

#### Using the sequential executor
The SequentialExecutor runs all the tasks after each other.
```php
use Nusje2000\ParallelProcess\Executor\SequentialExecutor;
use Nusje2000\ParallelProcess\TaskList;

$executor = new SequentialExecutor();
$executor->execute(new TaskList([]));
```

### Listening for process updates
Sometimes the output from commands must be used within the parent process. This can be done by using a listener. Each
listener has an onTick function which is called when the child process state is checked.

#### Creating a listener using the callback listener
If you want to add a simple listen function to an executor than the CallbackListener can be used. This takes a callback
as argument and then uses that callback as tick function.
```php
use Nusje2000\ParallelProcess\Executor\ExecutorInterface;
use Nusje2000\ParallelProcess\Listener\CallbackListener;
use Nusje2000\ParallelProcess\TaskList;

$listener = new CallbackListener(static function (TaskList $taskList) {
    echo sprintf('There are %d running tasks.', $taskList->getRunningTasks()->count());
});

/** @var ExecutorInterface $executor */
$executor->addListener($listener);
```

#### Creating a listener using the ExecutionListener interface
Sometimes a listener is too complicated for a simple callback function, in this case you can create your own class that
implements the ExecutionListener interface to handle the tick.
```php
use Nusje2000\ParallelProcess\Executor\ExecutorInterface;
use Nusje2000\ParallelProcess\Listener\ExecutionListener;
use Nusje2000\ParallelProcess\TaskList;

class RunningTaskListener implements ExecutionListener
{
    public function onTick(TaskList $taskList) : void
    {
        echo sprintf('There are %d running tasks.', $taskList->getRunningTasks()->count());
    }

    public function getPriority() : int{
        return 0;
    }
}

/** @var ExecutorInterface $executor */
$executor->addListener(new RunningTaskListener());
```

### Forwarding the status of the tasks to the console
In most cases the parent process is a command as well. If you want to see the status of the child processes
in the output of the parent process, you can add the ConsoleListener to the executor. This will show a list of all the
tasks with their status and in case of an error, it will show the output and error output of the child process.
```php
use Nusje2000\ParallelProcess\Executor\ExecutorInterface;
use Nusje2000\ParallelProcess\Listener\ConsoleListener;
use Symfony\Component\Console\Output\ConsoleOutput;

/** @var ExecutorInterface $executor */
$executor->addListener(new ConsoleListener(new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, true)));
```
The output of the listener will look something like this:
```
echo "Hello world!" (success)
process name 1 (success)
process name 2 (success)
process name 3 (failed (exit code: 255))

Task "process name 3" failed (exit code: 255).
Error output:
PHP Fatal error:  Uncaught Exception: error in Command line code:1
Stack trace:
#0 {main}
  thrown in Command line code on line 1
```

#### Using the console listener in situations where the output is logged
Is some situations (like ci/cd integrations), the output of console commands is logged to files. If this is the case,
using the default ConsoleLogger would result in output for each process change check (by default 5 times a second).
For these usecases, you can use the StaticConsoleListener. This listener will only print the changes to the console,
instead of updating the console output using sections.
```php
use Nusje2000\ParallelProcess\Executor\ExecutorInterface;
use Nusje2000\ParallelProcess\Listener\StaticConsoleListener;
use Symfony\Component\Console\Output\ConsoleOutput;

/** @var ExecutorInterface $executor */
$executor->addListener(new StaticConsoleListener(new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, true)));
```
The output of the listener will look something like this:
```
echo "Hello world!" is running
echo "Hello world!" is successfull
process name 1 is running
process name 1 is successfull
process name 2 is running
process name 2 is successfull
process name 3 is running
process name 3 has failed
Error output:
PHP Fatal error:  Uncaught Exception: Some exception message in Command line code:1
Stack trace:
#0 {main}
  thrown in Command line code on line 1
```
