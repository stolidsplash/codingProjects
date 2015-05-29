/*Joni Carlson
carlsjon@onid.oregonstate.edu
CS311-400
Homework6
Credits:
1. http://www.mathcs.emory.edu/~cheung/Courses/255/Syllabus/1-C-intro/bit-array.html
*/
/*THIS IS THE MULTIPROCESS VERSION*/

#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <getopt.h>
#include <fcntl.h>
#include <time.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <string.h>
#include <strings.h>
#include <ctype.h>
#include <sys/mman.h>
#include <errno.h>

#define SetBit(A,k)     ( A[(k/32)] |= (1 << (k%32)) )
#define ClearBit(A,k)   ( A[(k/32)] &= ~(1 << (k%32)) )
#define TestBit(A,k)    ( A[(k/32)] & (1 << (k%32)) )

int isPrime(int num);
static void spawnchild(int childNum, int rangeMin, int rangeMax);
static void waitforchild(void);
void createBitArray(int* array, int mNumber);
void createsharedMemory(int maxPrime, const char* memspace, void* addr);
void cleanUpSharedMemory(const char *memspace);

int main(int argc, char **argv)
{
    /* variables */
	int check, maxPrime, numProcesses, testnum, index, childNum, rangeMin, rangeMax;
	int size;
	int * bitArray;
	const char* memspace;
	void* addr;
	addr = NULL;
	
	/*PROCESS COMMAND LINE*/
while ((check = getopt(argc, argv, "qm:c:v:P")) != -1) 
    {
        switch (check)
        {
        case 'm': /*value of the largest number to be checked as prime*/
   			maxPrime = atoi(optarg);
            break;
		case 'c': /*number of processes to use in calculations*/
			if(optarg)
			{
				numProcesses = atoi(optarg);
			}
			else
			{
				numProcesses = 1;
			}
            break;
		case 'q': /*print primes to the monitor for testing*/
		/*CHECK PRIMES*/
			for (testnum = 1; testnum < 50000; testnum++)
			{
				if((isPrime(testnum) == 0) && (isPrime(testnum+2)== 0))
				{
					printf("first prime= %d, second = %d\n", testnum, testnum+2);
				}
			}
			break;
        case 'v':
			printf("\n\n");
            break;
		case 'P':
			printf("P");
            break;
		default:
			break;
        }
    }
	
	/*create bit array*/
	size = maxPrime*sizeof(int)/32;
	
	bitArray = (int*)malloc(maxPrime*sizeof(int));
	createBitArray(bitArray, maxPrime);
	
	/*create shared memory space*/
	memspace = (char*)malloc(maxPrime*sizeof(int));
	createsharedMemory(maxPrime, memspace, addr);
	//void* bitarraymemoryblockthatisshared = mmap(NULL, <size of memory needed>,
	//PROT_READ|PROT_WRITE, MAP_SHARED|MAP_ANONYMOUS, -1, 0)
	/*fork children & have them process primes*/
	if (numProcesses == 1)
	{
		childNum = numProcesses;
		rangeMin = 0;
		rangeMax = maxPrime;
		spawnchild(childNum, rangeMin, rangeMax);
	}
	else
	{
		for(index = 1; index < numProcesses; index++)
		{
			/*get process number & range of number for each child to process*/
				childNum = index;
				rangeMin = index-1 * maxPrime/numProcesses;
				rangeMax = index*(maxPrime/numProcesses);
				spawnchild(childNum, rangeMin, rangeMax);
			
		}
	}
	
	/*clean up stray children*/
	waitforchild();
	/*release filedes*/
	close(filedes);
	/*free memory*/
	free(memspace);
	free(bitArray);
	/*clean up shared memory object*/
	cleanUpSharedMemory(memspace);
   return 0;
}

int isPrime(int num)
{ 
	int i;
	i = 0;
	if(num == 1){return 1;}
	else if(num == 2){return 0;}
	else if((num % 2) == 0){	return 1;}
	else 
	{
		for(i = 3; i < num; i += 2)
		{
			if(num % i == 0)
			{
				return 1;
			}
		}
		return 0;
	}
}

static void spawnchild(int childNum, int rangeMin, int rangeMax)
{
	pid_t chPid;
	
	switch (chPid = fork())
	{
		case -1:
			perror("fork failure");
		exit(EXIT_FAILURE);
			break;
		case 0:
		/* Perform actions specific to child */
		printf("forked one child\n");
		
			exit(EXIT_SUCCESS);
			break;
		default:
			break;
	}
}
static void waitforchild(void)
{
	pid_t chPid;

	chPid = wait(NULL);
	if (chPid == -1) {
		if (errno == ECHILD) {
			exit(EXIT_SUCCESS);
		} 
		else /* Some other (unexpected) error */
		{       
			perror("wait");
		}
	}
	printf("wait() returned child PID %ld\n",(long) chPid);
}

void createBitArray(int* bitArray, int maxPrime)
{
   int i;

   for ( i = 0; i < maxPrime; i++ )
      bitArray[i] = 0;                    // Clear the bit array
	
   printf("Set bit positions 100, 200 and 300\n");
   SetBit( bitArray, 100 );               // Set 3 bits
   SetBit(bitArray, 200 );
   SetBit( bitArray, 300 );

   //Check if SetBit() works:

   for ( i = 0; i < 32*maxPrime; i++ )
      if ( TestBit(bitArray, i) )
         printf("Bit %d was set !\n", i);

   printf("\nClear bit positions 200 \n");
   ClearBit(bitArray, 200 );

   // Check if ClearBit() works:

   for ( i = 0; i < 320; i++ )
      if ( TestBit(bitArray, i) )
         printf("Bit %d was set !\n", i);
	printf("bitarray\n");
}
void createsharedMemory(int maxPrime, const char* memspace, void* addr)
{
	int filedes;
	size_t size;
	size_t temp;
	struct stat sb;
	
	/*create shared memory object with read & write permissions */
	filedes = shm_open("memspace", O_CREAT | O_RDWR, S_IRUSR | S_IWUSR);
	printf("filedes %d\n", filedes);
	if (fstat(filedes, &sb) == -1)           /* To obtain file size */
        printf("fstat problem");
	printf("size is %ld\n", (long)sb.st_size);
	
	/*set its size, starting at 0, then enlarging to size*/
	size = maxPrime* (sizeof(int));
	
	if (ftruncate(filedes, size) == -1)
	{
		perror("ftruncate");
		exit(EXIT_FAILURE);
	}
	printf("size is after %ld\n", (long)size);
	/*map shared memory object*/
	addr = mmap(NULL, size, PROT_READ | PROT_WRITE, MAP_SHARED, filedes, 0);
	if (addr == MAP_FAILED)
		exit(EXIT_FAILURE);
	//printf("addr ptr is %p\n", addr);
}
void cleanUpSharedMemory(const char *memspace, size_t size, void* addr)
{
	if(shm_unlink(memspace)== 0)
	{
		printf("cleanup shared memory");
	}
	else
	{
		printf("shared mem clean fail");
	}
}

